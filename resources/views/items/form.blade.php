<x-layouts.app :title="$mode === 'create' ? 'Tambah Barang' : 'Edit Barang'">
    <div class="card">
        <h2>{{ $mode === 'create' ? 'Tambah Barang' : 'Edit Barang' }}</h2>
        <form method="POST" action="{{ $mode === 'create' ? route('superadmin.items.store') : route('superadmin.items.update', $item) }}" class="form-grid">
            @csrf
            @if ($mode === 'edit') @method('PUT') @endif

            <div class="grid grid-2">
                <div>
                    <label for="code">Kode Barang</label>
                    <input id="code" name="code" value="{{ old('code', $item->code) }}" required>
                    @error('code') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="name">Nama Barang</label>
                    <input id="name" name="name" value="{{ old('name', $item->name) }}" required>
                    @error('name') <div class="error-text">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description">{{ old('description', $item->description) }}</textarea>
                @error('description') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div>
                <label>
                    <input type="checkbox" name="is_active" value="1" style="width:auto;" @checked(old('is_active', $item->is_active))>
                    Barang aktif
                </label>
            </div>

            <div class="actions">
                <button class="button button-primary" type="submit">Simpan</button>
                <a class="button" href="{{ route('superadmin.items.index') }}">Batal</a>
            </div>
        </form>
    </div>
</x-layouts.app>
