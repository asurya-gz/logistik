<x-layouts.app :title="$mode === 'create' ? 'Set Harga' : 'Edit Harga'">
    <div class="p-3 sm:p-5">
        <div class="mx-auto max-w-xl space-y-4">

            {{-- Header --}}
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">{{ $mode === 'create' ? 'Set Harga Baru' : 'Edit Harga' }}</h1>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $mode === 'create' ? 'Atur harga barang berlaku mulai tanggal tertentu' : 'Perbarui data harga' }}</p>
                </div>
                <a href="{{ route('superadmin.prices.index') }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-medium text-slate-500 hover:bg-slate-50 transition-colors">
                    ← Kembali
                </a>
            </div>

            {{-- Form --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="POST"
                    action="{{ $mode === 'create' ? route('superadmin.prices.store') : route('superadmin.prices.update', $price) }}"
                    class="space-y-4">
                    @csrf
                    @if ($mode === 'edit') @method('PUT') @endif

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label for="item_id" class="block text-xs font-medium text-slate-600">Barang</label>
                            <select id="item_id" name="item_id" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100">
                                <option value="">Pilih barang</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" @selected((int) old('item_id', $price->item_id) === $item->id)>{{ $item->name }}</option>
                                @endforeach
                            </select>
                            @error('item_id') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label for="branch_id" class="block text-xs font-medium text-slate-600">Cabang <span class="text-slate-400 font-normal">(kosong = global)</span></label>
                            <select id="branch_id" name="branch_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100">
                                <option value="">Global</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected((int) old('branch_id', $price->branch_id) === $branch->id)>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            @error('branch_id') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label for="price" class="block text-xs font-medium text-slate-600">Harga (Rp)</label>
                            <input id="price" name="price" type="number" min="0" step="0.01"
                                value="{{ old('price', $price->price) }}" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100">
                            @error('price') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label for="effective_date" class="block text-xs font-medium text-slate-600">Tanggal Berlaku</label>
                            <input id="effective_date" name="effective_date" type="date"
                                value="{{ old('effective_date', optional($price->effective_date)->format('Y-m-d') ?? $price->effective_date) }}" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100">
                            @error('effective_date') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="notes" class="block text-xs font-medium text-slate-600">Catatan <span class="text-slate-400 font-normal">(opsional)</span></label>
                        <textarea id="notes" name="notes" rows="3"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 outline-none transition resize-none focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                        >{{ old('notes', $price->notes) }}</textarea>
                        @error('notes') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('superadmin.prices.index') }}"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-medium text-slate-500 hover:bg-slate-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-medium text-white hover:bg-slate-700 transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-layouts.app>
