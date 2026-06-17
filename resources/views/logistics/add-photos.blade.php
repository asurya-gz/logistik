<x-layouts.app :title="'Tambah Foto Laporan'">
    <div class="card">
        <div class="mb-4">
            <h2>Tambah Foto</h2>
            <p class="muted">
                Laporan dari <strong>{{ $logistics->creator?->name ?? $logistics->nama_barang }}</strong>
                &middot; {{ $logistics->tanggal->format('d M Y') }}
                &middot; {{ $logistics->branch->name }}
            </p>
            <p class="muted mt-1">
                Sudah ada {{ $logistics->photos->count() }} foto &mdash; bisa tambah maksimal {{ 10 - $logistics->photos->count() }} foto lagi.
            </p>
        </div>

        {{-- Foto yang sudah ada --}}
        @if ($logistics->photos->isNotEmpty())
            <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-3">Foto Saat Ini</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                    @foreach ($logistics->photos as $photo)
                        <div class="space-y-1">
                            <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank" rel="noopener">
                                <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                     alt="Foto {{ $loop->iteration }}"
                                     class="w-full aspect-square object-cover rounded-lg border border-slate-200 hover:opacity-90 transition">
                            </a>
                            <p class="text-xs text-center text-slate-500">
                                Foto {{ $loop->iteration }}
                                @if ($photo->tanggal)
                                    <br>{{ $photo->tanggal->format('d M Y') }}
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST"
              action="{{ route('admin.logistics.add-photos', $logistics) }}"
              enctype="multipart/form-data"
              class="space-y-5"
              id="add-photos-form">
            @csrf

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-slate-700">
                        Foto Baru
                        <span class="text-slate-400 font-normal">(maks. {{ 10 - $logistics->photos->count() }})</span>
                    </label>
                    <button type="button" id="add-photo-btn"
                        class="text-xs font-medium text-slate-700 border border-slate-300 bg-white rounded-full px-3 py-1 hover:border-slate-400 transition">
                        + Tambah Foto
                    </button>
                </div>

                <div id="photo-slots" class="space-y-3"></div>

                @error('photos')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
                @error('photos.*')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-slate-800 transition">
                    Simpan &amp; Kembalikan ke Pending
                </button>
                <a href="{{ route('admin.logistics.index') }}"
                    class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <template id="photo-slot-tpl">
        <div class="photo-slot rounded-xl border border-slate-200 bg-slate-50 p-3 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide slot-label"></span>
                <button type="button" class="remove-slot-btn text-xs text-red-500 hover:text-red-700 transition hidden">Hapus</button>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <input type="file" name="photos[]" accept="image/*" required
                    class="flex-1 min-w-0 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg px-3 py-2 outline-none
                           file:mr-2 file:border-0 file:bg-slate-100 file:px-2 file:py-1 file:rounded file:text-slate-700 file:text-xs
                           focus:border-slate-400 transition">
                <input type="date" name="photo_dates[]"
                    class="w-full sm:w-40 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg px-3 py-2 outline-none
                           focus:border-slate-400 transition">
            </div>
            <div class="photo-preview hidden">
                <img class="preview-img h-20 w-auto rounded-lg object-cover border border-slate-200" src="" alt="">
            </div>
        </div>
    </template>

    <script>
        (() => {
            const MAX = {{ 10 - $logistics->photos->count() }};
            const slotsContainer = document.getElementById('photo-slots');
            const addBtn = document.getElementById('add-photo-btn');
            const tpl = document.getElementById('photo-slot-tpl');
            const existingCount = {{ $logistics->photos->count() }};

            const updateLabels = () => {
                const slots = slotsContainer.querySelectorAll('.photo-slot');
                slots.forEach((slot, i) => {
                    slot.querySelector('.slot-label').textContent = `Foto ${existingCount + i + 1}`;
                    slot.querySelector('.remove-slot-btn').classList.toggle('hidden', slots.length <= 1);
                });
                addBtn.disabled = slots.length >= MAX;
                addBtn.classList.toggle('opacity-40', slots.length >= MAX);
            };

            const addSlot = () => {
                if (slotsContainer.querySelectorAll('.photo-slot').length >= MAX) return;

                const clone = tpl.content.cloneNode(true);
                const slot = clone.querySelector('.photo-slot');

                slot.querySelector('.remove-slot-btn').addEventListener('click', () => {
                    slot.remove();
                    updateLabels();
                });

                const fileInput = slot.querySelector('input[type="file"]');
                const preview = slot.querySelector('.photo-preview');
                const previewImg = slot.querySelector('.preview-img');

                fileInput.addEventListener('change', () => {
                    const file = fileInput.files[0];
                    if (file) {
                        previewImg.src = URL.createObjectURL(file);
                        preview.classList.remove('hidden');
                    } else {
                        preview.classList.add('hidden');
                    }
                });

                slotsContainer.appendChild(clone);
                updateLabels();
            };

            addBtn.addEventListener('click', addSlot);
            addSlot();
        })();
    </script>
</x-layouts.app>
