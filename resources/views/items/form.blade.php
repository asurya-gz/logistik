<x-layouts.app :title="$mode === 'create' ? 'Tambah Barang' : 'Edit Barang'">
    <div class="p-3 sm:p-5">
        <div class="mx-auto max-w-xl space-y-4">

            {{-- Header --}}
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">{{ $mode === 'create' ? 'Tambah Barang' : 'Edit Barang' }}</h1>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $mode === 'create' ? 'Tambah barang baru ke daftar' : 'Perbarui informasi barang' }}</p>
                </div>
                <a href="{{ route('superadmin.items.index') }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-medium text-slate-500 hover:bg-slate-50 transition-colors">
                    ← Kembali
                </a>
            </div>

            {{-- Form --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="POST"
                    action="{{ $mode === 'create' ? route('superadmin.items.store') : route('superadmin.items.update', $item) }}"
                    class="space-y-4">
                    @csrf
                    @if ($mode === 'edit') @method('PUT') @endif

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label for="code" class="block text-xs font-medium text-slate-600">Kode Barang</label>
                            <input id="code" name="code" value="{{ old('code', $item->code) }}" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100">
                            @error('code') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label for="name" class="block text-xs font-medium text-slate-600">Nama Barang</label>
                            <input id="name" name="name" value="{{ old('name', $item->name) }}" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100">
                            @error('name') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="description" class="block text-xs font-medium text-slate-600">Deskripsi <span class="text-slate-400 font-normal">(opsional)</span></label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 outline-none transition resize-none focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                        >{{ old('description', $item->description) }}</textarea>
                        @error('description') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                            class="h-4 w-4 rounded border-slate-300 accent-slate-900"
                            @checked(old('is_active', $item->is_active))>
                        <span class="text-sm text-slate-700">Barang aktif</span>
                    </label>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('superadmin.items.index') }}"
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
