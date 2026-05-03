<x-layouts.app :title="'Informasi Lapangan'">
    @php
        $activeFilters = collect([
            $selectedBranchId ? 'Cabang' : null,
            $selectedItemId ? 'Barang' : null,
            $selectedStatus ? 'Status' : null,
            $dateFrom || $dateTo ? 'Tanggal' : null,
            ($minPrice !== null && $minPrice !== '') || ($maxPrice !== null && $maxPrice !== '') ? 'Harga' : null,
        ])->filter()->count();
    @endphp

    <div class="space-y-4 p-3 sm:p-5">
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                <div class="max-w-2xl">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Logistik Operasional</p>
                    <h1 class="mt-2 text-xl font-semibold leading-tight text-slate-900 sm:text-2xl">
                        Pantau transaksi lapangan dengan tampilan yang lebih ringkas.
                    </h1>
                    <p class="mt-2 max-w-xl text-sm leading-6 text-slate-500">
                        Data barang, nilai transaksi, dokumentasi foto, dan catatan operasional ditampilkan lebih padat agar lebih cepat dibaca saat monitoring harian.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:w-[360px]">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Total Data</p>
                        <p class="mt-1 text-lg font-semibold text-slate-900">{{ number_format($items->total()) }}</p>
                    </div>
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3">
                        <p class="text-[11px] font-medium uppercase tracking-wide text-emerald-600">Ditampilkan</p>
                        <p class="mt-1 text-lg font-semibold text-emerald-800">{{ number_format($items->count()) }}</p>
                    </div>
                    <div class="rounded-xl border border-cyan-100 bg-cyan-50 px-4 py-3">
                        <p class="text-[11px] font-medium uppercase tracking-wide text-cyan-600">Filter Aktif</p>
                        <p class="mt-1 text-lg font-semibold text-cyan-800">{{ $activeFilters }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Daftar Informasi</h2>
                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Gunakan filter untuk mempersempit pencarian barang, tanggal, status, dan nilai transaksi.
                    </p>
                </div>
                <a
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-slate-800"
                    href="{{ route(auth()->user()->panelRouteName('logistics.create')) }}"
                >
                    + Tambah Transaksi
                </a>
            </div>

            <form method="GET" class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @if ($user->isFullAccess())
                    <div class="space-y-1">
                        <label for="branch_id" class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Cabang</label>
                        <select
                            id="branch_id"
                            name="branch_id"
                            class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                        >
                            <option value="">Semua cabang</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected($selectedBranchId === $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="space-y-1">
                    <label for="item_id" class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Barang</label>
                    <select
                        id="item_id"
                        name="item_id"
                        class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                    >
                        <option value="">Semua barang</option>
                        @foreach ($itemOptions as $option)
                            <option value="{{ $option->id }}" @selected($selectedItemId === $option->id)>{{ $option->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label for="status" class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Status</label>
                    <select
                        id="status"
                        name="status"
                        class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                    >
                        <option value="">Semua status</option>
                        <option value="pending" @selected($selectedStatus === 'pending')>Pending</option>
                        <option value="approved" @selected($selectedStatus === 'approved')>Approved</option>
                        <option value="rejected" @selected($selectedStatus === 'rejected')>Rejected</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label for="date_from" class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Tanggal Mulai</label>
                    <input
                        id="date_from"
                        name="date_from"
                        type="date"
                        value="{{ $dateFrom }}"
                        class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                    >
                </div>
                <div class="space-y-1">
                    <label for="date_to" class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Tanggal Akhir</label>
                    <input
                        id="date_to"
                        name="date_to"
                        type="date"
                        value="{{ $dateTo }}"
                        class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                    >
                </div>
                <div class="space-y-1">
                    <label for="min_price" class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Harga Minimum</label>
                    <input
                        id="min_price"
                        name="min_price"
                        type="number"
                        min="0"
                        step="0.01"
                        value="{{ $minPrice }}"
                        class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                    >
                </div>
                <div class="space-y-1">
                    <label for="max_price" class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Harga Maksimum</label>
                    <input
                        id="max_price"
                        name="max_price"
                        type="number"
                        min="0"
                        step="0.01"
                        value="{{ $maxPrice }}"
                        class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                    >
                </div>
                <div class="grid grid-cols-2 gap-2 self-end xl:col-span-1">
                    <button class="rounded-xl bg-slate-900 px-3 py-2.5 text-sm font-medium text-white transition-colors hover:bg-slate-800" type="submit">
                        Terapkan
                    </button>
                    <a
                        href="{{ route(auth()->user()->panelRouteName('logistics.index')) }}"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-center text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50"
                    >
                        Reset
                    </a>
                </div>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 sm:px-5">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Transaksi Terbaru</h2>
                    <p class="mt-1 text-xs text-slate-500">Tampilan dibuat lebih padat agar barang, nilai, dan tindakan lebih cepat discan.</p>
                </div>
            </div>

            <div class="table-wrap">
                <table class="min-w-[1120px]">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Foto</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Total</th>
                            <th>Tanggal</th>
                            <th>Cabang</th>
                            <th>Status</th>
                            <th>Catatan Lapangan</th>
                            <th>Catatan Kantor</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>
                                    <div class="min-w-[180px]">
                                        <p class="text-sm font-semibold text-slate-900">{{ $item->item?->name ?? $item->nama_barang }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500">{{ $item->creator?->name ?? 'Tanpa pelapor' }}</p>
                                    </div>
                                </td>
                                <td>
                                    @if ($item->photos->isNotEmpty())
                                        <div class="min-w-[110px]">
                                            <p class="text-sm font-medium text-slate-800">{{ $item->photos->count() }} foto</p>
                                            <a class="mt-0.5 inline-block text-xs text-cyan-700 hover:text-cyan-800" href="{{ \Illuminate\Support\Facades\Storage::url($item->photos->first()->photo_path) }}" target="_blank">Lihat foto pertama</a>
                                        </div>
                                    @elseif ($item->photo_path)
                                        <a class="text-sm text-cyan-700 hover:text-cyan-800" href="{{ \Illuminate\Support\Facades\Storage::url($item->photo_path) }}" target="_blank">Lihat foto</a>
                                    @else
                                        <span class="text-xs text-slate-400">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="text-sm font-medium text-slate-700">{{ number_format($item->jumlah) }}</td>
                                <td class="text-sm text-slate-700">{{ $item->unit_price_snapshot !== null ? 'Rp ' . number_format((float) $item->unit_price_snapshot, 0, ',', '.') : '-' }}</td>
                                <td class="text-sm font-semibold text-slate-900">{{ $item->total_price !== null ? 'Rp ' . number_format((float) $item->total_price, 0, ',', '.') : '-' }}</td>
                                <td class="text-sm text-slate-700">{{ $item->tanggal->format('d M Y') }}</td>
                                <td class="text-sm text-slate-700">{{ $item->branch->name }}</td>
                                <td><x-status-badge :status="$item->status" /></td>
                                <td>
                                    <div class="max-w-[220px] text-sm leading-6 text-slate-600">
                                        {{ $item->keterangan ?: '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="min-w-[220px]">
                                        @if ($user->canAddOfficeNote())
                                            <form method="POST" action="{{ route(auth()->user()->panelRouteName('logistics.office-note'), $item) }}" class="space-y-2">
                                                @csrf
                                                @method('PATCH')
                                                <textarea
                                                    name="office_note"
                                                    placeholder="Tambahkan catatan"
                                                    class="min-h-[84px] rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                                                >{{ old('office_note', $item->office_note) }}</textarea>
                                                <button class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 transition-colors hover:bg-slate-50" type="submit">
                                                    Simpan Catatan
                                                </button>
                                            </form>
                                        @else
                                            <div class="max-w-[220px] text-sm leading-6 text-slate-600">
                                                {{ $item->office_note ?: '-' }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="flex min-w-[120px] flex-col gap-2">
                                        @if ($user->canEditInformation())
                                            <a
                                                class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-center text-xs font-medium text-slate-700 transition-colors hover:bg-slate-50"
                                                href="{{ route(auth()->user()->panelRouteName('logistics.edit'), $item) }}"
                                            >
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route(auth()->user()->panelRouteName('logistics.destroy'), $item) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    class="w-full rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-medium text-red-600 transition-colors hover:bg-red-100"
                                                    type="submit"
                                                    onclick="return confirm('Hapus data ini?')"
                                                >
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-slate-400">Hanya lihat</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11">
                                    <div class="m-3 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                                        Belum ada informasi lapangan yang cocok dengan filter saat ini.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-4 py-3 sm:px-5">
                <div class="pagination">{{ $items->links() }}</div>
            </div>
        </section>
    </div>
</x-layouts.app>
