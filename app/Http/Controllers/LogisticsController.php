<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Item;
use App\Models\ItemPrice;
use App\Models\Logistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LogisticsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $selectedBranchId = $request->integer('branch_id');
        $selectedItemId = $request->integer('item_id');
        $selectedStatus = $request->string('status')->toString();
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        $query = Logistics::query()
            ->with(['branch', 'creator', 'item', 'photos', 'logistikNotedBy'])
            ->visibleTo($user)
            ->latest('tanggal');

        if ($selectedBranchId) {
            $query->where('branch_id', $selectedBranchId);
        }

        if ($selectedStatus) {
            $query->where('status', $selectedStatus);
        }

        if ($selectedItemId) {
            $query->where('item_id', $selectedItemId);
        }

        if ($dateFrom) {
            $query->whereDate('tanggal', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('tanggal', '<=', $dateTo);
        }

        if ($minPrice !== null && $minPrice !== '') {
            $query->where('unit_price_snapshot', '>=', (float) $minPrice);
        }

        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('unit_price_snapshot', '<=', (float) $maxPrice);
        }

        return view('logistics.index', [
            'items' => $query->paginate(10)->withQueryString(),
            'branches' => Branch::query()->orderBy('name')->get(),
            'itemOptions' => Item::query()->where('is_active', true)->orderBy('name')->get(),
            'selectedBranchId' => $selectedBranchId,
            'selectedItemId' => $selectedItemId,
            'selectedStatus' => $selectedStatus,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'user' => $user,
        ]);
    }

    public function create(Request $request)
    {
        return view('logistics.form', [
            'item' => new Logistics(['tanggal' => now()->toDateString(), 'status' => 'pending']),
            'branches' => Branch::query()->orderBy('name')->get(),
            'itemOptions' => Item::query()->where('is_active', true)->orderBy('name')->get(),
            'mode' => 'create',
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $data = $this->validatedData($request, $user, true);
        $data['branch_id'] = $user->isFullAccess() ? ($data['branch_id'] ?? null) : $user->branch_id;
        $data['created_by'] = $user->id;
        $data['status'] = 'pending';
        $data = $this->enrichWithItemPricing($data);

        $logistics = Logistics::create($data);
        $this->syncPhotos($request, $logistics, true);

        return redirect()->route($user->panelRouteName('logistics.index'))->with('success', 'Informasi lapangan berhasil ditambahkan.');
    }

    public function show(Request $request, Logistics $logistics)
    {
        return redirect()->route($request->user()->panelRouteName('logistics.edit'), $logistics);
    }

    public function edit(Request $request, Logistics $logistics)
    {
        $this->authorizeAccess($request->user(), $logistics);

        if (! $request->user()->canEditInformation()) {
            abort(403);
        }

        $logistics->loadMissing('photos');

        return view('logistics.form', [
            'item' => $logistics,
            'branches' => Branch::query()->orderBy('name')->get(),
            'itemOptions' => Item::query()->where('is_active', true)->orderBy('name')->get(),
            'mode' => 'edit',
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request, Logistics $logistics)
    {
        $user = $request->user();
        $this->authorizeAccess($user, $logistics);

        if (! $user->canEditInformation()) {
            abort(403);
        }

        $data = $this->validatedData($request, $user, false);
        $data['branch_id'] = $user->isFullAccess() ? ($data['branch_id'] ?? null) : $user->branch_id;
        $data['status'] = 'pending';
        $data = $this->enrichWithItemPricing($data);

        $logistics->update($data);
        $this->syncPhotos($request, $logistics, false);

        return redirect()->route($user->panelRouteName('logistics.index'))->with('success', 'Informasi berhasil diperbarui.');
    }

    public function destroy(Request $request, Logistics $logistics)
    {
        $user = $request->user();
        $this->authorizeAccess($user, $logistics);

        if (! $user->canEditInformation()) {
            abort(403);
        }

        if ($logistics->photo_path) {
            Storage::disk('public')->delete($logistics->photo_path);
        }

        foreach ($logistics->photos as $photo) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        $logistics->delete();

        return redirect()->route($user->panelRouteName('logistics.index'))->with('success', 'Informasi berhasil dihapus.');
    }

    public function addPhotosForm(Request $request, Logistics $logistics)
    {
        $user = $request->user();

        if (! $user->canAddPhotosToRejected()) {
            abort(403);
        }

        $this->authorizeAccess($user, $logistics);

        if ($logistics->status !== 'rejected') {
            return redirect()->route($user->panelRouteName('logistics.index'))
                ->with('error', 'Hanya laporan yang ditolak yang bisa ditambah foto.');
        }

        $logistics->loadMissing('photos');

        return view('logistics.add-photos', [
            'logistics' => $logistics,
            'user' => $user,
        ]);
    }

    public function addPhotos(Request $request, Logistics $logistics)
    {
        $user = $request->user();

        if (! $user->canAddPhotosToRejected()) {
            abort(403);
        }

        $this->authorizeAccess($user, $logistics);

        if ($logistics->status !== 'rejected') {
            abort(422, 'Hanya laporan yang ditolak yang bisa ditambah foto.');
        }

        $existing = $logistics->photos()->count();
        $remaining = 10 - $existing;

        $request->validate([
            'photos'        => ['required', 'array', "max:{$remaining}"],
            'photos.*'      => ['image', 'max:4096'],
            'photo_dates'   => ['nullable', 'array'],
            'photo_dates.*' => ['nullable', 'date'],
        ]);

        $photoDates = $request->input('photo_dates', []);
        $firstPath  = null;

        foreach ($request->file('photos', []) as $index => $file) {
            $storedPath = $file->store('logistics-photos', 'public');
            $firstPath ??= $storedPath;

            $logistics->photos()->create([
                'photo_path' => $storedPath,
                'sort_order' => $existing + $index,
                'tanggal'    => $photoDates[$index] ?? null,
            ]);
        }

        $logistics->update([
            'status'      => 'pending',
            'photo_path'  => $firstPath ?? $logistics->photo_path,
        ]);

        return redirect()->route($user->panelRouteName('logistics.index'))
            ->with('success', 'Foto berhasil ditambahkan dan laporan dikembalikan ke pending.');
    }

    public function updateOfficeNote(Request $request, Logistics $logistics)
    {
        $user = $request->user();
        $this->authorizeAccess($user, $logistics);

        if (! $user->canAddOfficeNote()) {
            abort(403);
        }

        $data = $request->validate([
            'office_note' => ['nullable', 'string'],
        ]);

        $logistics->update([
            'office_note' => $data['office_note'] ?? null,
        ]);

        return redirect()->route($user->panelRouteName('logistics.index'))->with('success', 'Catatan berhasil diperbarui.');
    }

    private function validatedData(Request $request, $user, bool $creating): array
    {
        if (! $user->canEditInformation()) {
            return $request->validate([
                'keterangan' => ['required', 'string'],
                'photos' => [$creating ? 'required' : 'nullable', 'array', 'max:10'],
                'photos.*' => ['image', 'max:4096'],
            ]) + [
                'nama_barang' => $user->name,
                'kategori' => 'masuk',
                'jumlah' => 1,
                'tanggal' => now()->toDateString(),
            ];
        }

        return $request->validate([
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'kategori' => ['required', 'string', Rule::in(['masuk', 'keluar'])],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['required', 'string'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'max:4096'],
            'branch_id' => [$user->isFullAccess() ? 'required' : 'nullable', 'integer', 'exists:branches,id'],
        ]);
    }

    private function enrichWithItemPricing(array $data): array
    {
        if (empty($data['item_id'])) {
            return $data;
        }

        $item = Item::find($data['item_id']);

        if (! $item) {
            return $data;
        }

        $unitPrice = $this->resolveUnitPrice($item->id, $data['branch_id'] ?? null, $data['tanggal']);

        $data['nama_barang'] = $item->name;
        $data['unit_price_snapshot'] = $unitPrice;
        $data['total_price'] = $unitPrice !== null ? ($unitPrice * (int) $data['jumlah']) : null;

        return $data;
    }

    private function resolveUnitPrice(int $itemId, ?int $branchId, string $date): ?float
    {
        if ($branchId) {
            $branchPrice = ItemPrice::query()
                ->where('item_id', $itemId)
                ->where('branch_id', $branchId)
                ->effectiveOn($date)
                ->latest('effective_date')
                ->latest('id')
                ->value('price');

            if ($branchPrice !== null) {
                return (float) $branchPrice;
            }
        }

        $globalPrice = ItemPrice::query()
            ->where('item_id', $itemId)
            ->whereNull('branch_id')
            ->effectiveOn($date)
            ->latest('effective_date')
            ->latest('id')
            ->value('price');

        return $globalPrice !== null ? (float) $globalPrice : null;
    }

    private function syncPhotos(Request $request, Logistics $logistics, bool $creating): void
    {
        if (! $request->hasFile('photos')) {
            return;
        }

        foreach ($logistics->photos as $photo) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        if (! $creating && $logistics->photo_path) {
            Storage::disk('public')->delete($logistics->photo_path);
        }

        $logistics->photos()->delete();

        $firstPath = null;

        foreach ($request->file('photos', []) as $index => $file) {
            $storedPath = $file->store('logistics-photos', 'public');
            $firstPath ??= $storedPath;

            $logistics->photos()->create([
                'photo_path' => $storedPath,
                'sort_order' => $index,
            ]);
        }

        if ($firstPath) {
            $logistics->updateQuietly([
                'photo_path' => $firstPath,
            ]);
        }
    }

    private function authorizeAccess($user, Logistics $logistics): void
    {
        if (! $user->isFullAccess() && $user->branch_id !== $logistics->branch_id) {
            abort(403);
        }
    }
}
