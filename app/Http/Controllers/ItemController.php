<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->canManageItems(), 403);

        return view('items.index', [
            'items' => Item::query()->latest()->paginate(10),
        ]);
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->canManageItems(), 403);

        return view('items.form', [
            'item' => new Item(['is_active' => true]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->canManageItems(), 403);

        Item::create($this->validatedData($request));

        return redirect()->route('superadmin.items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Request $request, Item $item)
    {
        abort_unless($request->user()->canManageItems(), 403);

        return view('items.form', [
            'item' => $item,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Item $item)
    {
        abort_unless($request->user()->canManageItems(), 403);

        $item->update($this->validatedData($request, $item));

        return redirect()->route('superadmin.items.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Request $request, Item $item)
    {
        abort_unless($request->user()->canManageItems(), 403);

        $item->delete();

        return redirect()->route('superadmin.items.index')->with('success', 'Barang berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Item $item = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('items', 'code')->ignore($item?->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]) + [
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
