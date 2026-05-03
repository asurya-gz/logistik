<x-layouts.app :title="$mode === 'create' ? 'Tambah Cabang' : 'Edit Cabang'">
    <div class="card">
        <h2>{{ $mode === 'create' ? 'Tambah Cabang' : 'Edit Cabang' }}</h2>
        <form method="POST" action="{{ $mode === 'create' ? route('superadmin.branches.store') : route('superadmin.branches.update', $branch) }}" class="form-grid">
            @csrf
            @if ($mode === 'edit') @method('PUT') @endif

            <div>
                <label for="name">Nama Cabang</label>
                <input id="name" name="name" value="{{ old('name', $branch->name) }}" required>
                @error('name') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="code">Kode</label>
                <input id="code" name="code" value="{{ old('code', $branch->code) }}" required>
                @error('code') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="address">Alamat</label>
                <textarea id="address" name="address">{{ old('address', $branch->address) }}</textarea>
                @error('address') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="actions">
                <button class="button button-primary" type="submit">Simpan</button>
                <a class="button" href="{{ route('superadmin.branches.index') }}">Batal</a>
            </div>
        </form>
    </div>
</x-layouts.app>
