<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Item;
use App\Models\Logistics;
use App\Models\LogisticsPhoto;
use App\Models\LogisticsPhotoItem;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VerificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user->canViewVerifications()) {
            abort(403);
        }

        $selectedBranchId = $request->integer('branch_id');

        $query = Logistics::query()
            ->with(['branch', 'creator', 'photos', 'logistikNotedBy', 'supportingPhotos.uploader'])
            ->with(['photos.items.item'])  // eager load photo items + item names
            ->where('status', 'pending')
            ->latest('tanggal');

        if (! $user->isFullAccess()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($selectedBranchId) {
            $query->where('branch_id', $selectedBranchId);
        }

        return view('verifications.index', [
            'items' => $query->paginate(10)->withQueryString(),
            'branches' => Branch::orderBy('name')->get(),
            'itemList' => Item::where('is_active', true)->orderBy('name')->get(),
            'selectedBranchId' => $selectedBranchId,
            'user' => $user,
        ]);    }

    public function update(Request $request, Logistics $logistics)
    {
        $user = $request->user();

        if (! $user->canVerify()) {
            abort(403);
        }

        if (! $user->isFullAccess() && $user->branch_id !== $logistics->branch_id) {
            abort(403);
        }

        if ($logistics->status !== 'pending') {
            return back()->with('error', 'Hanya data berstatus pending yang bisa diverifikasi.');
        }

        $data = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'note' => ['nullable', 'string'],
        ]);

        if ($data['status'] === 'rejected' && blank($data['note'] ?? null)) {
            return back()->withErrors(['note' => 'Catatan wajib diisi saat reject.']);
        }

        DB::transaction(function () use ($data, $logistics, $user): void {
            $logistics->update([
                'status' => $data['status'],
            ]);

            Verification::create([
                'logistics_id' => $logistics->id,
                'status' => $data['status'],
                'note' => $data['note'] ?? null,
                'verified_by' => $user->id,
                'tanggal_verifikasi' => now(),
            ]);
        });

        return redirect()->route($user->panelRouteName('verifications.index'))->with('success', 'Data berhasil diverifikasi.');
    }

    public function updateLogistikNote(Request $request, Logistics $logistics)
    {
        $user = $request->user();

        if (! $user->canWriteLogistikNote()) {
            abort(403);
        }

        if ($user->branch_id !== $logistics->branch_id) {
            abort(403);
        }

        $data = $request->validate([
            'logistik_note' => ['required', 'string', 'max:2000'],
        ]);

        $logistics->update([
            'logistik_note'    => $data['logistik_note'],
            'logistik_noted_by' => $user->id,
            'logistik_noted_at' => now(),
        ]);

        return back()->with('success', 'Catatan berhasil disimpan.');
    }

    public function updatePhotoStatus(Request $request, LogisticsPhoto $photo)
    {
        $user = $request->user();

        if (! $user->canVerify()) {
            abort(403);
        }

        $data = $request->validate([
            'status' => ['required', Rule::in([LogisticsPhoto::STATUS_OK, LogisticsPhoto::STATUS_REJECT])],
        ]);

        $photo->update(['status' => $data['status']]);

        return back()->with('success', 'Status foto berhasil diperbarui.');
    }

    public function addPhotoItem(Request $request, LogisticsPhoto $photo)
    {
        $user = $request->user();
        abort_unless($user->canViewVerifications(), 403);

        // Only allow if the parent logistics is still pending
        abort_if($photo->logistics->status !== 'pending', 404);

        $data = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:9999'],
        ]);

        $photo->items()->create([
            'item_id' => $data['item_id'],
            'quantity' => $data['quantity'],
        ]);

        return back()->with('success', 'Barang berhasil ditambahkan ke foto.');
    }

    public function removePhotoItem(Request $request, LogisticsPhotoItem $photoItem)
    {
        $user = $request->user();
        abort_unless($user->canViewVerifications(), 403);

        // Only allow if the parent logistics is still pending
        abort_if($photoItem->photo->logistics->status !== 'pending', 404);

        $photoItem->delete();

        return back()->with('success', 'Barang dihapus dari foto.');
    }
}
