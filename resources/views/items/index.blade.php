<x-layouts.app :title="'Master Barang'">
    <div class="card">
        <div style="display:flex;justify-content:space-between;gap:1rem;align-items:center;flex-wrap:wrap;">
            <div>
                <h2>Master Barang</h2>
                <p class="muted">Kelola daftar barang yang akan digunakan pada transaksi logistik dan pengaturan harga.</p>
            </div>
            <a class="button button-primary" href="{{ route('superadmin.items.create') }}">Tambah Barang</a>
        </div>

        <div class="table-wrap" style="margin-top:1rem;">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Status</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>{{ $item->code }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                            <td>{{ $item->description ?: '-' }}</td>
                            <td>
                                <div class="actions">
                                    <a class="button" href="{{ route('superadmin.items.edit', $item) }}">Edit</a>
                                    <form class="inline" method="POST" action="{{ route('superadmin.items.destroy', $item) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button" type="submit" onclick="return confirm('Hapus barang ini?')">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty">Belum ada barang.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $items->links() }}</div>
    </div>
</x-layouts.app>
