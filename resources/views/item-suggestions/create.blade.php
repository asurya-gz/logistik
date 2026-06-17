<x-layouts.app :title="'Usulkan Barang Baru'">
    <div class="p-3 sm:p-5">
        <div class="mx-auto max-w-xl space-y-4">

            {{-- Header --}}
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">Usulkan Barang Baru</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Sarankan nama barang yang belum ada di master data</p>
                </div>
                <a href="{{ route(auth()->user()->panelRouteName('item-suggestions.index')) }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-medium text-slate-500 hover:bg-slate-50 transition-colors">
                    ← Kembali
                </a>
            </div>

            {{-- Info box --}}
            <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 text-amber-500 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-amber-800">Barang belum tersedia?</p>
                        <p class="text-xs text-amber-600 mt-1">
                            Usulan Anda akan ditinjau oleh Manajemen Kantor. Jika disetujui, barang akan otomatis ditambahkan ke master data.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="POST"
                    action="{{ route('admin.item-suggestions.store') }}"
                    class="space-y-4">
                    @csrf

                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-medium text-slate-600">Nama Barang</label>
                        <input id="name" name="name" value="{{ old('name') }}" required
                            class="w-full rounded-xl border {{ $errors->has('name') ? 'border-red-300' : 'border-slate-200' }} bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                            placeholder="Contoh: Besi Hollow 4x4">
                        @error('name')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="notes" class="block text-xs font-medium text-slate-600">Keterangan <span class="text-slate-400 font-normal">(opsional)</span></label>
                        <textarea id="notes" name="notes" rows="3"
                            class="w-full rounded-xl border {{ $errors->has('notes') ? 'border-red-300' : 'border-slate-200' }} bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition resize-none focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                            placeholder="Deskripsi atau alasan mengusulkan barang ini...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <a href="{{ route(auth()->user()->panelRouteName('item-suggestions.index')) }}"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-medium text-slate-500 hover:bg-slate-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-medium text-white hover:bg-slate-700 transition-colors">
                            Kirim Usulan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-layouts.app>
