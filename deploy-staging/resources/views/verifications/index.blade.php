<x-layouts.app :title="'Verifikasi Data'">
    <div class="card">
        <h2>Verifikasi Data Pending</h2>
        <p class="muted">Verifikasi ini khusus M. Kantor untuk tindak lanjut akhir atas informasi yang masuk.</p>

        <form method="GET" class="toolbar" style="margin:1rem 0;">
            @if ($user->isFullAccess())
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
            <div class="actions">
                <button class="button button-primary" type="submit">Filter</button>
            </div>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Cabang</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Tanggal</th>
                        <th>Catatan Reject</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>{{ $item->nama_barang }}</td>
                            <td>{{ $item->branch->name }}</td>
                            <td>{{ ucfirst($item->kategori) }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>{{ $item->tanggal->format('d M Y') }}</td>
                            <td>
                                <form method="POST" action="{{ route(auth()->user()->panelRouteName('verifications.update'), $item) }}" class="form-grid">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <textarea name="note" placeholder="Wajib diisi jika reject"></textarea>
                                    <button class="button" type="submit">Reject</button>
                                </form>
                            </td>
                            <td>
                                <div class="actions">
                                    <form class="inline" method="POST" action="{{ route(auth()->user()->panelRouteName('verifications.update'), $item) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button class="button button-primary" type="submit">Approve</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="empty">Tidak ada data pending.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $items->links() }}</div>
    </div>
</x-layouts.app>
