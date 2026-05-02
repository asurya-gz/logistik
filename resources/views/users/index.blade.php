<x-layouts.app :title="'User'">
    <div class="card">
        <div style="display:flex;justify-content:space-between;gap:1rem;align-items:center;flex-wrap:wrap;">
            <div>
                <h2>Manajemen User</h2>
                <p class="muted">Kelola akun super admin dan admin cabang dari satu tempat.</p>
            </div>
            <a class="button button-primary" href="{{ route('superadmin.users.create') }}">Tambah User</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Cabang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ \App\Models\User::roleOptions()[$user->role] ?? $user->role }}</td>
                        <td>{{ $user->branch?->name ?: '-' }}</td>
                        <td>
                            <div class="actions">
                                <a class="button" href="{{ route('superadmin.users.edit', $user) }}">Edit</a>
                                <form class="inline" method="POST" action="{{ route('superadmin.users.destroy', $user) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button" type="submit" onclick="return confirm('Hapus user ini?')">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty">Belum ada user.</div></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $users->links() }}</div>
    </div>
</x-layouts.app>
