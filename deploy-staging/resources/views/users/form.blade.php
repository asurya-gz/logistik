<x-layouts.app :title="$mode === 'create' ? 'Tambah User' : 'Edit User'">
    <div class="card">
        <h2>{{ $mode === 'create' ? 'Tambah User' : 'Edit User' }}</h2>
        <form method="POST" action="{{ $mode === 'create' ? route('superadmin.users.store') : route('superadmin.users.update', $userModel) }}" class="form-grid">
            @csrf
            @if ($mode === 'edit') @method('PUT') @endif

            <div class="grid grid-2">
                <div>
                    <label for="name">Nama</label>
                    <input id="name" name="name" value="{{ old('name', $userModel->name) }}" required>
                    @error('name') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $userModel->email) }}" required>
                    @error('email') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        @foreach ($roles as $value => $label)
                            <option value="{{ $value }}" @selected(old('role', $userModel->role ?: \App\Models\User::ROLE_LAPANGAN) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="identity_number">Nomor Identitas Lapangan</label>
                    <input id="identity_number" name="identity_number" value="{{ old('identity_number', $userModel->identity_number) }}" placeholder="Wajib untuk M. Lapangan">
                    @error('identity_number') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="branch_id">Cabang</label>
                    <select id="branch_id" name="branch_id">
                        <option value="">Tanpa cabang</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((int) old('branch_id', $userModel->branch_id) === $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="password">Password {{ $mode === 'edit' ? '(kosongkan jika tidak diubah)' : '' }}</label>
                    <input id="password" name="password" type="password" {{ $mode === 'create' ? 'required' : '' }}>
                    @error('password') <div class="error-text">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="actions">
                <button class="button button-primary" type="submit">Simpan</button>
                <a class="button" href="{{ route('superadmin.users.index') }}">Batal</a>
            </div>
        </form>
    </div>
</x-layouts.app>
