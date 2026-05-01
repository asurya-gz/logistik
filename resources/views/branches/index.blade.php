<x-layouts.app :title="'Cabang'">
    <div class="card">
        <div style="display:flex;justify-content:space-between;gap:1rem;align-items:center;flex-wrap:wrap;">
            <div>
                <h2>Manajemen Cabang</h2>
                <p class="muted">Hanya super admin yang dapat menambah, mengubah, dan menghapus cabang.</p>
            </div>
            <a class="button button-primary" href="{{ route('branches.create') }}">Tambah Cabang</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Kode</th>
                    <th>Alamat</th>
                    <th>User</th>
                    <th>Data Logistik</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($branches as $branch)
                    <tr>
                        <td>{{ $branch->name }}</td>
                        <td>{{ $branch->code }}</td>
                        <td>{{ $branch->address ?: '-' }}</td>
                        <td>{{ $branch->users_count }}</td>
                        <td>{{ $branch->logistics_count }}</td>
                        <td>
                            <div class="actions">
                                <a class="button" href="{{ route('branches.edit', $branch) }}">Edit</a>
                                <form class="inline" method="POST" action="{{ route('branches.destroy', $branch) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button" type="submit" onclick="return confirm('Hapus cabang ini?')">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty">Belum ada cabang.</div></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $branches->links() }}</div>
    </div>
</x-layouts.app>
