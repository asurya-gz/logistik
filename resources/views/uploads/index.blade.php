<x-layouts.app :title="'Upload Data'">
    <div class="grid grid-2">
        <div class="card">
            <h2>Upload Data Logistik</h2>
            <p class="muted">Kolom yang didukung: <code>nama_barang</code>, <code>kategori</code>, <code>jumlah</code>, <code>tanggal</code>, <code>keterangan</code>.</p>
            <form method="POST" action="{{ route(auth()->user()->panelRouteName('uploads.store')) }}" enctype="multipart/form-data" class="form-grid">
                @csrf
                @if ($user->isSuperAdmin())
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
            <div class="pagination">{{ $uploads->links() }}</div>
        </div>
    </div>
</x-layouts.app>
