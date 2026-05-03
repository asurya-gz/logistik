<x-layouts.app :title="$mode === 'create' ? 'Tambah Informasi' : 'Edit Informasi'">
    <div class="card">
        <h2>{{ $mode === 'create' ? 'Upload Informasi Lapangan' : 'Edit Informasi' }}</h2>
        <form method="POST" action="{{ $mode === 'create' ? route(auth()->user()->panelRouteName('logistics.store')) : route(auth()->user()->panelRouteName('logistics.update'), $item) }}" enctype="multipart/form-data" class="form-grid">
            @csrf
            @if ($mode === 'edit') @method('PUT') @endif

            @if ($user->canEditInformation())
                <div class="grid grid-2">
                    <div>
                        <label for="item_id">Barang</label>
                        <select id="item_id" name="item_id" required>
                            <option value="">Pilih barang</option>
                            @foreach ($itemOptions as $option)
                                <option value="{{ $option->id }}" @selected((int) old('item_id', $item->item_id) === $option->id)>{{ $option->name }}</option>
                            @endforeach
                        </select>
                        @error('item_id') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="kategori">Kategori</label>
                        <select id="kategori" name="kategori" required>
                            <option value="masuk" @selected(old('kategori', $item->kategori) === 'masuk')>Masuk</option>
                            <option value="keluar" @selected(old('kategori', $item->kategori) === 'keluar')>Keluar</option>
                        </select>
                        @error('kategori') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="jumlah">Jumlah</label>
                        <input id="jumlah" name="jumlah" type="number" min="1" value="{{ old('jumlah', $item->jumlah) }}" required>
                        @error('jumlah') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="tanggal">Tanggal</label>
                        <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', optional($item->tanggal)->format('Y-m-d') ?? $item->tanggal) }}" required>
                        @error('tanggal') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    @if ($user->isFullAccess())
                        <div>
                            <label for="branch_id">Cabang</label>
                            <select id="branch_id" name="branch_id" required>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected((int) old('branch_id', $item->branch_id) === $branch->id)>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            @error('branch_id') <div class="error-text">{{ $message }}</div> @enderror
                        </div>
                    @endif
                </div>
            @else
                <div class="card" style="padding:1rem;border-radius:18px;">
                    <p class="muted" style="margin:0;">
                        Nama pelapor akan otomatis mengikuti akun yang login, dan tanggal akan otomatis mengikuti waktu upload.
                    </p>
                </div>
            @endif

            <div class="grid grid-2">
                <div>
                    <label for="photos">Foto</label>
                    <input id="photos" name="photos[]" type="file" accept="image/*" multiple {{ $mode === 'create' ? 'required' : '' }}>
                    <div class="muted">Maksimal 10 foto per data.</div>
                    @error('photos') <div class="error-text">{{ $message }}</div> @enderror
                    @error('photos.*') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                @if (($item->relationLoaded('photos') && $item->photos->isNotEmpty()) || $item->photo_path)
                    <div>
                        <label>Foto Saat Ini</label>
                        <div class="actions">
                            @foreach ($item->photos ?? collect() as $photo)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($photo->photo_path) }}" target="_blank" class="button">Foto {{ $loop->iteration }}</a>
                            @endforeach
                            @if (($item->photos ?? collect())->isEmpty() && $item->photo_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($item->photo_path) }}" target="_blank" class="button">Lihat Foto</a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div>
                <label for="keterangan">Keterangan Lapangan</label>
                <textarea id="keterangan" name="keterangan" required>{{ old('keterangan', $item->keterangan) }}</textarea>
                @error('keterangan') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            @if ($user->canEditInformation())
                <div>
                    <label for="office_note">Catatan Kantor</label>
                    <textarea id="office_note" name="office_note" disabled>{{ $item->office_note }}</textarea>
                    <div class="muted">Catatan kantor dikelola dari daftar informasi.</div>
                </div>
            @endif
            <div class="actions">
                <button class="button button-primary" type="submit">Simpan</button>
                <a class="button" href="{{ route(auth()->user()->panelRouteName('logistics.index')) }}">Batal</a>
            </div>
        </form>
    </div>
</x-layouts.app>
