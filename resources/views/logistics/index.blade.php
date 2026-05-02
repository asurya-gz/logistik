<x-layouts.app :title="'Informasi Lapangan'">
    <div class="card">
        <div style="display:flex;justify-content:space-between;gap:1rem;align-items:center;flex-wrap:wrap;">
            <div>
                <h2>Informasi Lapangan</h2>
                <p class="muted">M. Lapangan mengirim foto dan keterangan. Officer / M. Logistik dapat memberi catatan. M. Kantor dapat mengelola penuh.</p>
            </div>
            <a class="button button-primary" href="{{ route(auth()->user()->panelRouteName('logistics.create')) }}">Upload Informasi</a>
        </div>

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
                    <th>Pelapor</th>
                    <th>Foto</th>
                    <th>Tanggal</th>
                    <th>Cabang</th>
                    <th>Status</th>
                    <th>Keterangan Lapangan</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>
                            <div>{{ $item->creator?->name ?? $item->nama_barang }}</div>
                            <div class="muted" style="font-size:.85rem;">{{ $item->nama_barang }}</div>
                        </td>
                        <td>
                            @if ($item->photo_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($item->photo_path) }}" target="_blank">Lihat foto</a>
                            @else
                                <span class="muted">Tidak ada</span>
                            @endif
                        </td>
                        <td>{{ $item->tanggal->format('d M Y') }}</td>
                        <td>{{ $item->branch->name }}</td>
                        <td><x-status-badge :status="$item->status" /></td>
                        <td>{{ $item->keterangan ?: '-' }}</td>
                        <td style="min-width:220px;">
                            @if ($user->canAddOfficeNote())
                                <form method="POST" action="{{ route(auth()->user()->panelRouteName('logistics.office-note'), $item) }}" class="form-grid">
                                    @csrf
                                    @method('PATCH')
                                    <textarea name="office_note" placeholder="Tambahkan catatan">{{ old('office_note', $item->office_note) }}</textarea>
                                    <button class="button" type="submit">Simpan Catatan</button>
                                </form>
                            @else
                                {{ $item->office_note ?: '-' }}
                            @endif
                        </td>
                        <td>
                            <div class="actions">
                                @if ($user->canEditInformation())
                                    <a class="button" href="{{ route(auth()->user()->panelRouteName('logistics.edit'), $item) }}">Edit</a>
                                    <form class="inline" method="POST" action="{{ route(auth()->user()->panelRouteName('logistics.destroy'), $item) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button" type="submit" onclick="return confirm('Hapus data ini?')">Hapus</button>
                                    </form>
                                @else
                                    <span class="muted">Hanya lihat</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8"><div class="empty">Belum ada informasi lapangan.</div></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $items->links() }}</div>
    </div>
</x-layouts.app>
