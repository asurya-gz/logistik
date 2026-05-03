<x-layouts.app :title="'Upload Data'">
    <div class="grid grid-2">
        <div class="card">
            <h2>Upload Excel</h2>
            <p class="muted">Fitur ini khusus M. Kantor untuk impor data massal. Format terbaru mendukung item, quantity, tanggal transaksi, dan akan otomatis mengambil snapshot harga aktif bila tersedia.</p>
            <div class="card" style="margin:1rem 0 1.2rem;padding:1rem;border-radius:18px;">
                <h3 style="margin-bottom:.6rem;">Format Kolom</h3>
                <p class="muted" style="margin-top:0;">
                    Kolom yang didukung:
                    <code>kode_barang</code>,
                    <code>nama_barang</code>,
                    <code>kategori</code>,
                    <code>jumlah</code>,
                    <code>tanggal</code>,
                    <code>keterangan</code>.
                </p>
                <div class="table-wrap" style="margin-top:.8rem;">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>BRG-001</code></td>
                                <td>Laptop ThinkPad</td>
                                <td>masuk</td>
                                <td>5</td>
                                <td>2026-05-03</td>
                                <td>Pengiriman batch pagi</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="muted" style="margin:.8rem 0 0;">
                    Jika <code>kode_barang</code> belum ada di master, sistem akan membuat master barang otomatis dari data upload.
                </p>
            </div>
            <form method="POST" action="{{ route(auth()->user()->panelRouteName('uploads.store')) }}" enctype="multipart/form-data" class="form-grid">
                @csrf
                @if ($user->isFullAccess())
                    <div>
                        <label for="branch_id">Cabang</label>
                        <select id="branch_id" name="branch_id" required>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                @endif
                <div>
                    <label for="file">File Excel / CSV</label>
                    <input id="file" name="file" type="file" accept=".csv,.txt,.xls,.xlsx" required>
                    @error('file') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                <button class="button button-primary" type="submit">Upload dan Proses</button>
            </form>
        </div>
        <div class="card">
            <h2>Riwayat Upload</h2>
            <p class="muted">Riwayat berikut mencatat file yang berhasil diproses ke transaksi logistik.</p>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>File</th>
                            <th>Cabang</th>
                            <th>Baris</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($uploads as $upload)
                            <tr>
                                <td>{{ $upload->file_name }}</td>
                                <td>{{ $upload->branch->name }}</td>
                                <td>{{ $upload->total_rows }}</td>
                                <td>{{ $upload->tanggal_upload->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><div class="empty">Belum ada riwayat upload.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $uploads->links() }}</div>
        </div>
    </div>
</x-layouts.app>
