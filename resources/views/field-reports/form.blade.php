<x-layouts.app :title="'Form Lapangan'">

    @if (session('success'))
    <div id="success-modal" class="fixed inset-0 z-50 flex items-center justify-center px-5 bg-slate-950/40 backdrop-blur-sm">
        <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl p-7 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50">
                <svg class="w-7 h-7 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6 9 17l-5-5"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-slate-900">Laporan Terkirim</h2>
            <p class="mt-1.5 text-sm text-slate-400">Informasi lapangan berhasil dikirim dan sedang menunggu verifikasi.</p>
            <button id="close-success-modal"
                class="mt-6 w-full rounded-xl bg-slate-900 hover:bg-slate-800 active:scale-[0.99] py-2.5 text-sm font-medium text-white transition-all duration-150">
                Kirim Laporan Lagi
            </button>
        </div>
    </div>
    @endif

    <div class="min-h-screen bg-white flex items-center justify-center px-5 py-12">
        <div class="w-full max-w-md">

            {{-- Logo --}}
            <div class="flex items-center gap-2.5 mb-10">
                <div class="w-8 h-8 bg-slate-900 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/>
                        <path d="M16.5 9.4 7.55 4.24"/>
                        <polyline points="3.29 7 12 12 20.71 7"/>
                        <line x1="12" x2="12" y1="22" y2="12"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-slate-900">Sistem Logistik</span>
            </div>

            {{-- Header --}}
            <div class="mb-7">
                <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">Form Lapangan</h1>
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-sm text-slate-400">
                        {{ $fieldUser->name }}
                        <span class="text-slate-200 mx-1.5">/</span>
                        <span class="font-mono">{{ $fieldUser->identity_number }}</span>
                    </p>
                    <form method="POST" action="{{ route('field-reports.logout') }}">
                        @csrf
                        <button type="submit" class="text-xs text-slate-400 hover:text-slate-700 transition-colors duration-150">
                            Ganti →
                        </button>
                    </form>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('field-reports.store') }}" enctype="multipart/form-data" class="space-y-5" id="field-report-form">
                @csrf

                {{-- Photos --}}
                <div>
                    <label class="text-sm font-medium text-slate-700 block mb-2">Foto <span class="text-slate-400 font-normal text-xs">maks. 10</span></label>
                    <div id="photo-slots" class="space-y-2"></div>
                    <p id="photo-error" class="hidden text-xs text-red-500 mt-1.5"></p>
                    @error('photos')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                    @error('photos.*')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                    <button type="button" id="add-photo-btn"
                        class="mt-2 w-full flex items-center justify-center gap-1.5 rounded-xl border border-dashed border-slate-300 py-2.5 text-xs font-medium text-slate-500 hover:border-slate-400 hover:text-slate-700 hover:bg-slate-50 transition-all duration-150">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Tambah Foto
                    </button>
                </div>

                {{-- Keterangan --}}
                <div>
                    <label for="keterangan" class="block text-sm font-medium text-slate-700 mb-1.5">Keterangan</label>
                    <textarea
                        id="keterangan"
                        name="keterangan"
                        rows="4"
                        required
                        placeholder="Tuliskan keterangan laporan..."
                        class="w-full px-4 py-2.5 text-sm text-slate-900 bg-slate-50 border rounded-xl outline-none transition-all duration-200 resize-none
                               placeholder:text-slate-300
                               {{ $errors->has('keterangan') ? 'border-red-300 focus:border-red-400 focus:ring-3 focus:ring-red-50' : 'border-slate-200 focus:border-slate-400 focus:bg-white focus:ring-3 focus:ring-slate-100' }}"
                    >{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Foto Pendukung (opsional) --}}
                <div class="border-t border-slate-100 pt-5">
                    <label class="text-sm font-medium text-slate-700 block mb-1">Foto Pendukung <span class="text-slate-400 font-normal text-xs">(opsional, maks. 5)</span></label>
                    <p class="text-xs text-slate-400 mb-2">Foto tambahan sebagai referensi. Bisa ditambahkan nanti oleh logistik.</p>
                    <input type="file" name="supporting_photos[]" accept="image/*" multiple
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 file:transition-colors">
                </div>

                <button type="submit"
                    class="w-full bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white text-sm font-medium py-2.5 rounded-xl transition-all duration-150">
                    Kirim Laporan
                </button>
            </form>

        </div>
    </div>

    <template id="photo-slot-tpl">
        <div class="photo-slot rounded-xl border border-slate-200 overflow-hidden">

            {{-- Slot header --}}
            <div class="flex items-center justify-between px-3.5 py-2.5 bg-slate-50 border-b border-slate-200">
                <span class="slot-label text-xs font-semibold text-slate-500 uppercase tracking-wide">Foto 1</span>
                <button type="button" class="remove-slot-btn hidden text-xs text-red-400 hover:text-red-600 transition-colors duration-150">Hapus</button>
            </div>

            {{-- Preview (hidden until file chosen) --}}
            <div class="photo-preview hidden relative">
                <img class="preview-img w-full max-h-48 object-cover" src="" alt="">
                <div class="absolute inset-0 bg-linear-to-t from-black/30 to-transparent"></div>
            </div>

            {{-- Inputs --}}
            <div class="p-3 space-y-2 bg-white">
                <label class="block cursor-pointer group">
                    <input type="file" name="photos[]" accept="image/*" class="sr-only file-input">
                    <div class="flex items-center gap-2.5 w-full border border-dashed border-slate-300 rounded-lg px-3.5 py-2.5
                                group-hover:border-slate-400 group-hover:bg-slate-50 transition-all duration-150">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                            <circle cx="9" cy="9" r="2"/>
                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                        </svg>
                        <span class="file-label text-xs text-slate-400 truncate">Pilih foto...</span>
                    </div>
                </label>

                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-slate-300 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                        <line x1="16" x2="16" y1="2" y2="6"/>
                        <line x1="8" x2="8" y1="2" y2="6"/>
                        <line x1="3" x2="21" y1="10" y2="10"/>
                    </svg>
                    <input type="date" name="photo_dates[]"
                        class="flex-1 text-xs text-slate-700 bg-transparent outline-none transition-colors duration-150
                               placeholder:text-slate-300 focus:text-slate-900">
                </div>
            </div>
        </div>
    </template>

    <script>
        (() => {
            const MAX   = 10;
            const slots = document.getElementById('photo-slots');
            const addBtn = document.getElementById('add-photo-btn');
            const tpl   = document.getElementById('photo-slot-tpl');

            const updateLabels = () => {
                const all = slots.querySelectorAll('.photo-slot');
                all.forEach((slot, i) => {
                    slot.querySelector('.slot-label').textContent = `Foto ${i + 1}`;
                    slot.querySelector('.remove-slot-btn').classList.toggle('hidden', all.length <= 1);
                });
                addBtn.disabled = all.length >= MAX;
                addBtn.classList.toggle('opacity-30', all.length >= MAX);
            };

            const addSlot = () => {
                if (slots.querySelectorAll('.photo-slot').length >= MAX) return;
                const slot = tpl.content.cloneNode(true).querySelector('.photo-slot');

                slot.querySelector('.remove-slot-btn').addEventListener('click', () => {
                    slot.remove(); updateLabels();
                });

                const input   = slot.querySelector('.file-input');
                const label   = slot.querySelector('.file-label');
                const preview = slot.querySelector('.photo-preview');
                const img     = slot.querySelector('.preview-img');

                input.addEventListener('change', () => {
                    const file = input.files[0];
                    if (file) {
                        label.textContent = file.name;
                        img.src = URL.createObjectURL(file);
                        preview.classList.remove('hidden');
                    } else {
                        label.textContent = 'Pilih foto...';
                        preview.classList.add('hidden');
                    }
                });

                slots.appendChild(slot);
                updateLabels();
            };

            addBtn.addEventListener('click', addSlot);
            addSlot();

            // Sebelum submit: buang slot yang tidak ada file-nya
            document.getElementById('field-report-form').addEventListener('submit', function (e) {
                const allSlots = slots.querySelectorAll('.photo-slot');
                let hasFile = false;

                allSlots.forEach(slot => {
                    const input = slot.querySelector('.file-input');
                    if (!input.files || !input.files[0]) {
                        slot.remove();
                    } else {
                        hasFile = true;
                    }
                });

                if (!hasFile) {
                    e.preventDefault();
                    const err = document.getElementById('photo-error');
                    err.textContent = 'Pilih minimal satu foto sebelum mengirim laporan.';
                    err.classList.remove('hidden');
                    err.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    document.getElementById('photo-error').classList.add('hidden');
                }
            });
        })();

        // Success modal
        const successModal = document.getElementById('success-modal');
        if (successModal) {
            document.getElementById('close-success-modal').addEventListener('click', () => {
                successModal.classList.add('hidden');
            });
            successModal.addEventListener('click', e => {
                if (e.target === successModal) successModal.classList.add('hidden');
            });
        }
    </script>
</x-layouts.app>
