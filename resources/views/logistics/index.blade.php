<x-layouts.app :title="'Data Logistik'">
    <div class="card">
        <div style="display:flex;justify-content:space-between;gap:1rem;align-items:center;flex-wrap:wrap;">
            <div>
                <h2>Data Logistik</h2>
                <p class="muted">User cabang hanya melihat datanya sendiri. Super admin dapat melihat seluruh cabang.</p>
            </div>
            <a class="button button-primary" href="{{ route('logistics.create') }}">Tambah Data</a>
        </div>

        <form method="GET" class="toolbar" style="margin:1rem 0;">
            @if ($user->isSuperAdmin())
                <div>
                    <label for="branch_id">Cabang</label>
                    <select id="branch_id" name="branch_id">
                        <option value="">Semua cabang</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected($selectedBranchId === $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div>
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">Semua status</option>
                    <option value="pending" @selected($selectedStatus === 'pending')>Pending</option>
                    <option value="approved" @selected($selectedStatus === 'approved')>Approved</option>
                    <option value="rejected" @selected($selectedStatus === 'rejected')>Rejected</option>
                </select>
            </div>
            <div class="actions">
                <button class="button button-primary" type="submit">Filter</button>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Cabang</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ ucfirst($item->kategori) }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>{{ $item->tanggal->format('d M Y') }}</td>
                        <td>{{ $item->branch->name }}</td>
                        <td><x-status-badge :status="$item->status" /></td>
                        <td>{{ $item->keterangan ?: '-' }}</td>
                        <td>
                            <div class="actions">
                                <a class="button" href="{{ route('logistics.edit', $item) }}">Edit</a>
                                <form class="inline" method="POST" action="{{ route('logistics.destroy', $item) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button" type="submit" onclick="return confirm('Hapus data ini?')">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8"><div class="empty">Belum ada data logistik.</div></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $items->links() }}</div>
    </div>
</x-layouts.app>
