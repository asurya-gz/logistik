<x-layouts.app :title="$mode === 'create' ? 'Tambah Logistik' : 'Edit Logistik'">
    <div class="card">
        <h2>{{ $mode === 'create' ? 'Tambah Data Logistik' : 'Edit Data Logistik' }}</h2>
        <form method="POST" action="{{ $mode === 'create' ? route(auth()->user()->panelRouteName('logistics.store')) : route(auth()->user()->panelRouteName('logistics.update'), $item) }}" class="form-grid">
            @csrf
            @if ($mode === 'edit') @method('PUT') @endif

            <div class="grid grid-2">
                <div>
                    <label for="nama_barang">Nama Barang</label>
                    <input id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $item->nama_barang) }}" required>
                    @error('nama_barang') <div class="error-text">{{ $message }}</div> @enderror
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
                @if ($user->isSuperAdmin())
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
            <div>
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan">{{ old('keterangan', $item->keterangan) }}</textarea>
                @error('keterangan') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="actions">
                <button class="button button-primary" type="submit">Simpan</button>
                <a class="button" href="{{ route(auth()->user()->panelRouteName('logistics.index')) }}">Batal</a>
            </div>
        </form>
    </div>
</x-layouts.app>
