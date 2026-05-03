<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Logistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LogisticsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $selectedBranchId = $request->integer('branch_id');
        $selectedStatus = $request->string('status')->toString();

        $query = Logistics::query()
            ->with(['branch', 'creator'])
            ->visibleTo($user)
            ->latest('tanggal');

        if ($selectedBranchId) {
            $query->where('branch_id', $selectedBranchId);
        }

        if ($selectedStatus) {
            $query->where('status', $selectedStatus);
        }

        return view('logistics.index', [
            'items' => $query->paginate(10)->withQueryString(),
            'branches' => Branch::orderBy('name')->get(),
            'selectedBranchId' => $selectedBranchId,
            'selectedStatus' => $selectedStatus,
            'user' => $user,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('logistics.form', [
            'item' => new Logistics(['tanggal' => now()->toDateString(), 'status' => 'pending']),
            'branches' => Branch::orderBy('name')->get(),
            'mode' => 'create',
            'user' => $request->user(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $data = $this->validatedData($request, $user, true);
        $data['branch_id'] = $user->isFullAccess() ? ($data['branch_id'] ?? null) : $user->branch_id;
        $data['created_by'] = $user->id;
        $data['status'] = 'pending';

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('logistics-photos', 'public');
        }

        Logistics::create($data);

        return redirect()->route($user->panelRouteName('logistics.index'))->with('success', 'Informasi lapangan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Logistics $logistics)
    {
        return redirect()->route($request->user()->panelRouteName('logistics.edit'), $logistics);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Logistics $logistics)
    {
        $this->authorizeAccess($request->user(), $logistics);

        if (! $request->user()->canEditInformation()) {
            abort(403);
        }

        return view('logistics.form', [
            'item' => $logistics,
            'branches' => Branch::orderBy('name')->get(),
            'mode' => 'edit',
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
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

        if ($request->hasFile('photo')) {
            if ($logistics->photo_path) {
                Storage::disk('public')->delete($logistics->photo_path);
            }

            $data['photo_path'] = $request->file('photo')->store('logistics-photos', 'public');
        }

        $logistics->update($data);

        return redirect()->route($user->panelRouteName('logistics.index'))->with('success', 'Informasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
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

        $logistics->delete();

        return redirect()->route($user->panelRouteName('logistics.index'))->with('success', 'Informasi berhasil dihapus.');
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
                'photo' => [$creating ? 'required' : 'nullable', 'image', 'max:4096'],
            ]) + [
                'nama_barang' => $user->name,
                'kategori' => 'masuk',
                'jumlah' => 1,
                'tanggal' => now()->toDateString(),
            ];
        }

        return $request->validate([
            'nama_barang' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', Rule::in(['masuk', 'keluar'])],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['required', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'branch_id' => [$user->isFullAccess() ? 'required' : 'nullable', 'integer', 'exists:branches,id'],
        ]);
    }

    private function authorizeAccess($user, Logistics $logistics): void
    {
        if (! $user->isFullAccess() && $user->branch_id !== $logistics->branch_id) {
            abort(403);
        }
    }
}
