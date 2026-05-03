<x-layouts.app :title="'Pengaturan Harga'">
    <div class="card">
        <div style="display:flex;justify-content:space-between;gap:1rem;align-items:center;flex-wrap:wrap;">
            <div>
                <h2>Pengaturan Harga</h2>
                <p class="muted">Manajemen kantor dapat mengatur harga global atau harga khusus per cabang berdasarkan tanggal berlaku.</p>
            </div>
            <a class="button button-primary" href="{{ route('superadmin.prices.create') }}">Set Harga</a>
        </div>

        <form method="GET" class="toolbar" style="margin:1rem 0;">
            <div>
                <label for="item_id">Barang</label>
                <select id="item_id" name="item_id">
                    <option value="">Semua barang</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" @selected($selectedItemId === $item->id)>{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="branch_id">Cabang</label>
                <select id="branch_id" name="branch_id">
                    <option value="">Semua cabang / global</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" @selected($selectedBranchId === $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
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
                        <th>Harga</th>
                        <th>Tanggal Berlaku</th>
                        <th>Dibuat Oleh</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($prices as $price)
                        <tr>
                            <td>{{ $price->item->name }}</td>
                            <td>{{ $price->branch?->name ?: 'Global' }}</td>
                            <td>Rp {{ number_format((float) $price->price, 0, ',', '.') }}</td>
                            <td>{{ $price->effective_date->format('d M Y') }}</td>
                            <td>{{ $price->creator->name }}</td>
                            <td>{{ $price->notes ?: '-' }}</td>
                            <td>
                                <div class="actions">
                                    <a class="button" href="{{ route('superadmin.prices.edit', $price) }}">Edit</a>
                                    <form class="inline" method="POST" action="{{ route('superadmin.prices.destroy', $price) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button" type="submit" onclick="return confirm('Hapus harga ini?')">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="empty">Belum ada histori harga.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $prices->links() }}</div>
    </div>
</x-layouts.app>
