<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Item;
use App\Models\ItemPrice;
use Illuminate\Http\Request;

class ItemPriceController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->canManagePrices(), 403);

        $selectedItemId = $request->integer('item_id');
        $selectedBranchId = $request->integer('branch_id');

        $query = ItemPrice::query()
            ->with(['item', 'branch', 'creator'])
            ->latest('effective_date');

        if ($selectedItemId) {
            $query->where('item_id', $selectedItemId);
        }

        if ($selectedBranchId) {
            $query->where('branch_id', $selectedBranchId);
        }

        return view('item-prices.index', [
            'prices' => $query->paginate(10)->withQueryString(),
            'items' => Item::query()->orderBy('name')->get(),
            'branches' => Branch::query()->orderBy('name')->get(),
            'selectedItemId' => $selectedItemId,
            'selectedBranchId' => $selectedBranchId,
        ]);
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->canManagePrices(), 403);

        return view('item-prices.form', [
            'price' => new ItemPrice(['effective_date' => now()->toDateString()]),
            'items' => Item::query()->where('is_active', true)->orderBy('name')->get(),
            'branches' => Branch::query()->orderBy('name')->get(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        abort_unless($user->canManagePrices(), 403);

        ItemPrice::create($this->validatedData($request) + [
            'created_by' => $user->id,
        ]);

        return redirect()->route('superadmin.prices.index')->with('success', 'Harga berhasil ditambahkan.');
    }

    public function edit(Request $request, ItemPrice $price)
    {
        abort_unless($request->user()->canManagePrices(), 403);

        return view('item-prices.form', [
            'price' => $price,
            'items' => Item::query()->orderBy('name')->get(),
            'branches' => Branch::query()->orderBy('name')->get(),
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, ItemPrice $price)
    {
        abort_unless($request->user()->canManagePrices(), 403);

        $price->update($this->validatedData($request));

        return redirect()->route('superadmin.prices.index')->with('success', 'Harga berhasil diperbarui.');
    }

    public function destroy(Request $request, ItemPrice $price)
    {
        abort_unless($request->user()->canManagePrices(), 403);

        $price->delete();

        return redirect()->route('superadmin.prices.index')->with('success', 'Harga berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'effective_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
