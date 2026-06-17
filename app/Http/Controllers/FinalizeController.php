<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Logistics;
use Illuminate\Http\Request;

class FinalizeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user->canVerify()) {
            abort(403);
        }

        $tab              = $request->input('tab', 'pending');
        $selectedBranchId = $request->integer('branch_id');

        $query = Logistics::query()
            ->with(['branch', 'creator', 'photos', 'finalizedBy', 'supportingPhotos.uploader'])
            ->with('photos.items.item')  // eager load photo items with item names
            ->where('status', 'approved')
            ->latest('tanggal');

        if (! $user->isFullAccess()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($selectedBranchId) {
            $query->where('branch_id', $selectedBranchId);
        }

        if ($tab === 'done') {
            $query->whereNotNull('finalized_at');
        } else {
            $query->whereNull('finalized_at');
        }

        return view('finalisasi.index', [
            'items'            => $query->paginate(10)->withQueryString(),
            'branches'         => Branch::orderBy('name')->get(),
            'selectedBranchId' => $selectedBranchId,
            'tab'              => $tab,
            'user'             => $user,
        ]);
    }

    public function finalize(Request $request, Logistics $logistics)
    {
        $user = $request->user();

        if (! $user->canVerify()) {
            abort(403);
        }

        if ($logistics->status !== 'approved') {
            return back()->with('error', 'Hanya laporan yang sudah diapprove yang bisa difinalisasi.');
        }

        if ($logistics->isFinalized()) {
            return back()->with('error', 'Laporan ini sudah difinalisasi.');
        }

        $photoItems = $logistics->photos()->with('items')->get()
            ->flatMap(fn ($p) => $p->items)
            ->values();

        $itemCount = $photoItems->count();

        if ($itemCount === 0) {
            return back()->with('error', 'Laporan ini belum memiliki detail barang. Silakan isi detail barang di halaman verifikasi terlebih dahulu.');
        }

        $data = $request->validate([
            'item_prices'   => ['required', 'array', 'size:' . $itemCount],
            'item_prices.*' => ['required', 'numeric', 'min:0'],
        ]);

        $prices = array_map('floatval', $data['item_prices']);
        $total  = 0;

        foreach ($photoItems as $i => $item) {
            $subtotal = $prices[$i] * $item->quantity;
            $total += $subtotal;
            $item->update(['price' => $prices[$i]]);
        }

        $logistics->update([
            'jumlah'              => $logistics->photos()->with('items')->get()->flatMap->items->sum('quantity'),
            'unit_price_snapshot' => $itemCount > 0 ? $total / max($logistics->photos()->with('items')->get()->flatMap->items->sum('quantity'), 1) : 0,
            'total_price'         => $total,
            'finalized_at'        => now(),
            'finalized_by'        => $user->id,
        ]);

        return back()->with('success', 'Finalisasi berhasil disimpan.');
    }
}
