<x-layouts.app :title="'Informasi Lapangan'">
    @php
        $fmt = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
        $activeFilters = $user->isFullAccess()
            ? collect([$selectedBranchId ? 'Cabang' : null, $selectedItemId ? 'Barang' : null])->filter()->count()
            : collect([
                $selectedBranchId ? 'Cabang' : null,
                $selectedItemId ? 'Barang' : null,
                $selectedStatus ? 'Status' : null,
                $dateFrom || $dateTo ? 'Tanggal' : null,
                $minPrice !== null && $minPrice !== '' || $maxPrice !== null && $maxPrice !== '' ? 'Harga' : null,
            ])->filter()->count();
    @endphp

    <div class="space-y-4 p-3 sm:p-5">

        {{-- ═══ Topbar ═══ --}}
        <div class="flex items-center justify-between gap-4 flex-col sm:flex-row">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">Informasi Lapangan</h1>
                <p class="text-xs text-slate-400 mt-0.5">
                    {{ number_format($items->total()) }} laporan
                    @if ($activeFilters > 0)
                        &middot; <span class="text-indigo-600 font-medium">{{ $activeFilters }} filter aktif</span>
                    @endif
                </p>
            </div>
            <button type="button" id="toggle-filter"
                class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/>
                </svg>
                Filter
                @if ($activeFilters > 0)
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-indigo-600 text-[10px] font-bold text-white">{{ $activeFilters }}</span>
                @endif
            </button>
        </div>

        {{-- ═══ Filter Panel ═══ --}}
        <div id="filter-panel" class="{{ $activeFilters > 0 ? '' : 'hidden' }} rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            @if ($user->isFullAccess())
                <form method="GET" class="flex items-end gap-3 flex-wrap">
                    <div class="space-y-1 flex-1 min-w-[160px]">
                        <label class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Cabang</label>
                        <select name="branch_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-300 focus:bg-white focus:ring-2 focus:ring-indigo-100">
                            <option value="">Semua cabang</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected($selectedBranchId === $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1 flex-1 min-w-[160px]">
                        <label class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Barang</label>
                        <select name="item_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-300 focus:bg-white focus:ring-2 focus:ring-indigo-100">
                            <option value="">Semua barang</option>
                            @foreach ($itemOptions as $option)
                                <option value="{{ $option->id }}" @selected($selectedItemId === $option->id)>{{ $option->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-slate-800 transition-colors shadow-sm">Terapkan</button>
                        <a href="{{ route(auth()->user()->panelRouteName('logistics.index')) }}"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">Reset</a>
                    </div>
                </form>
            @else
                <form method="GET" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="space-y-1">
                        <label class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Barang</label>
                        <select name="item_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-300 focus:bg-white focus:ring-2 focus:ring-indigo-100">
                            <option value="">Semua barang</option>
                            @foreach ($itemOptions as $option)
                                <option value="{{ $option->id }}" @selected($selectedItemId === $option->id)>{{ $option->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Status</label>
                        <select name="status" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-300 focus:bg-white focus:ring-2 focus:ring-indigo-100">
                            <option value="">Semua status</option>
                            <option value="pending" @selected($selectedStatus === 'pending')>Pending</option>
                            <option value="approved" @selected($selectedStatus === 'approved')>Approved</option>
                            <option value="rejected" @selected($selectedStatus === 'rejected')>Rejected</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Tanggal Mulai</label>
                        <input type="date" name="date_from" value="{{ $dateFrom }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-300 focus:bg-white focus:ring-2 focus:ring-indigo-100">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Tanggal Akhir</label>
                        <input type="date" name="date_to" value="{{ $dateTo }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-300 focus:bg-white focus:ring-2 focus:ring-indigo-100">
                    </div>
                    @if ($user->canEditInformation())
                        <div class="space-y-1">
                            <label class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Harga Min</label>
                            <input type="number" name="min_price" min="0" step="0.01" value="{{ $minPrice }}" placeholder="0"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-300 focus:bg-white focus:ring-2 focus:ring-indigo-100">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Harga Maks</label>
                            <input type="number" name="max_price" min="0" step="0.01" value="{{ $maxPrice }}" placeholder="0"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-300 focus:bg-white focus:ring-2 focus:ring-indigo-100">
                        </div>
                    @endif
                    <div class="flex gap-2 self-end">
                        <button type="submit" class="flex-1 rounded-xl bg-slate-900 py-2.5 text-sm font-medium text-white hover:bg-slate-800 transition-colors shadow-sm">Terapkan</button>
                        <a href="{{ route(auth()->user()->panelRouteName('logistics.index')) }}"
                            class="flex-1 rounded-xl border border-slate-200 bg-white py-2.5 text-center text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">Reset</a>
                    </div>
                </form>
            @endif
        </div>

        {{-- ═══ Card List ═══ --}}
        <div class="space-y-3">
            @forelse ($items as $item)
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden hover:shadow-md transition-shadow">

                    {{-- Card header --}}
                    <div class="flex items-start justify-between gap-4 px-5 py-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl text-xs font-bold
                                    {{ $item->kategori === 'masuk' ? 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100' : 'bg-amber-50 text-amber-600 ring-1 ring-amber-100' }}">
                                    {{ $item->kategori === 'masuk' ? 'IN' : 'OUT' }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 truncate">
                                        {{ $item->item?->name ?? $item->nama_barang }}
                                    </p>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        {{ $item->creator?->name ?? 'Tanpa pelapor' }}
                                        &middot; {{ $item->branch->name }}
                                        &middot; {{ $item->tanggal->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <x-status-badge :status="$item->status" />

                            @if ($user->canEditInformation())
                                <a href="{{ route(auth()->user()->panelRouteName('logistics.edit'), $item) }}"
                                    class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-colors">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route(auth()->user()->panelRouteName('logistics.destroy'), $item) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus data ini?')"
                                        class="rounded-lg border border-red-200 bg-white px-2.5 py-1.5 text-xs font-medium text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                    </button>
                                </form>
                            @elseif ($user->canAddPhotosToRejected() && $item->status === 'rejected')
                                <a href="{{ route('admin.logistics.add-photos.form', $item) }}"
                                    class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-medium text-amber-700 hover:bg-amber-100 transition-colors">
                                    + Foto
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Stats row --}}
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 border-t border-slate-100 bg-slate-50/60 px-5 py-2.5">
                        <div class="flex items-center gap-1.5 text-xs text-slate-500">
                            <svg class="h-3.5 w-3.5 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                            </svg>
                            <span>{{ $item->photos->count() }} foto</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-slate-500">
                            <svg class="h-3.5 w-3.5 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 16V8a2 2 0 0 0-1.1-1.79l-6-3a2 2 0 0 0-1.8 0l-6 3A2 2 0 0 0 5 8v8a2 2 0 0 0 1.1 1.79l6 3a2 2 0 0 0 1.8 0l6-3A2 2 0 0 0 21 16Z"/>
                            </svg>
                            <span>Jumlah: <strong class="text-slate-700">{{ $item->jumlah }}</strong></span>
                        </div>
                        @if ($item->unit_price_snapshot !== null)
                            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                                <span class="text-slate-300">@</span>
                                <span class="font-medium text-slate-700">{{ $fmt($item->unit_price_snapshot) }}</span>
                            </div>
                        @endif
                        @if ($item->total_price !== null)
                            <div class="flex items-center gap-1.5 text-xs">
                                <span class="text-slate-300">=</span>
                                <span class="font-semibold text-slate-900">{{ $fmt($item->total_price) }}</span>
                            </div>
                        @endif

                        <div class="ml-auto flex items-center gap-2">
                            {{-- Lihat Foto button --}}
                            @if ($item->photos->isNotEmpty())
                                <button type="button"
                                    class="open-photo-modal flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100 transition-colors"
                                    data-photos='@json($item->photos->map(fn($p) => ["url" => \Illuminate\Support\Facades\Storage::url($p->photo_path), "tanggal" => $p->tanggal?->format("d M Y")])->values())'
                                    data-label="{{ $item->item?->name ?? $item->nama_barang }}">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                    </svg>
                                    {{ $item->photos->count() }} Foto
                                </button>
                            @elseif ($item->photo_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($item->photo_path) }}" target="_blank"
                                    class="flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                    </svg>
                                    1 Foto
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Keterangan & catatan --}}
                    @if ($item->keterangan || $user->canAddOfficeNote() || ($user->isFullAccess() && $item->logistik_note))
                        <div class="border-t border-slate-100 px-5 py-3.5 space-y-3">

                            @if ($item->keterangan)
                                <div class="flex gap-3">
                                    <svg class="h-4 w-4 text-slate-300 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                    </svg>
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-1">Catatan Lapangan</p>
                                        <p class="text-sm text-slate-600 leading-relaxed">{{ $item->keterangan }}</p>
                                    </div>
                                </div>
                            @endif

                            @if ($user->canAddOfficeNote())
                                <div class="flex gap-3" data-note-block>
                                    <svg class="h-4 w-4 text-slate-300 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                    </svg>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Catatan Logistik</p>
                                            <button type="button"
                                                class="text-xs text-indigo-500 hover:text-indigo-700 font-medium transition-colors note-edit-btn"
                                                data-note="{{ e($item->office_note) }}">
                                                {{ $item->office_note ? 'Edit' : '+ Tambah' }}
                                            </button>
                                        </div>

                                        <div class="note-read-view {{ $item->office_note ? '' : 'hidden' }}">
                                            <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $item->office_note }}</p>
                                        </div>

                                        <form method="POST"
                                            action="{{ route(auth()->user()->panelRouteName('logistics.office-note'), $item) }}"
                                            class="note-edit-form hidden gap-2" style="display:none">
                                            @csrf @method('PATCH')
                                            <textarea name="office_note" rows="2" placeholder="Tulis catatan..."
                                                class="flex-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none resize-none transition
                                                       placeholder:text-slate-300 focus:border-indigo-300 focus:bg-white focus:ring-2 focus:ring-indigo-100 note-textarea"
                                            >{{ old('office_note', $item->office_note) }}</textarea>
                                            <div class="flex gap-2 mt-2">
                                                <button type="submit"
                                                    class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-800 transition-colors">
                                                    Simpan
                                                </button>
                                                <button type="button"
                                                    class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-500 hover:bg-slate-50 transition-colors note-cancel-btn">
                                                    Batal
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @elseif ($user->isFullAccess() && $item->logistik_note)
                                <div class="flex gap-3">
                                    <svg class="h-4 w-4 text-slate-300 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                    </svg>
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-1">Catatan Logistik</p>
                                        <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $item->logistik_note }}</p>
                                        <p class="text-xs text-slate-400 mt-1">
                                            {{ $item->logistikNotedBy?->name ?? '—' }}
                                            @if ($item->logistik_noted_at)
                                                &middot; {{ $item->logistik_noted_at->format('d M Y, H:i') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-5 py-16 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-300">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-500">Belum ada data</p>
                    <p class="text-xs text-slate-400 mt-1">Tidak ada laporan yang cocok dengan filter saat ini.</p>
                </div>
            @endforelse
        </div>

        <div class="flex justify-end">{{ $items->links() }}</div>
    </div>

    {{-- Photo modal --}}
    <div id="photo-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm">
        <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl flex flex-col" style="max-height: 80vh;">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 shrink-0">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Foto Laporan</p>
                    <p class="mt-0.5 text-sm font-semibold text-slate-900" id="photo-modal-label"></p>
                </div>
                <button id="close-photo-modal"
                    class="flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 text-slate-400 hover:border-slate-300 hover:text-slate-700 transition">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div id="photo-modal-body" class="overflow-y-auto px-5 py-4 space-y-4"></div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.note-edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const block    = btn.closest('[data-note-block]');
                const readView = block.querySelector('.note-read-view');
                const editForm = block.querySelector('.note-edit-form');
                const textarea = block.querySelector('.note-textarea');

                readView.classList.add('hidden');
                editForm.style.display = 'block';
                editForm.classList.remove('hidden');
                textarea.focus();
            });
        });

        document.querySelectorAll('.note-cancel-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const block    = btn.closest('[data-note-block]');
                const readView = block.querySelector('.note-read-view');
                const editForm = block.querySelector('.note-edit-form');

                editForm.style.display = 'none';
                editForm.classList.add('hidden');
                readView.classList.remove('hidden');
            });
        });

        document.getElementById('toggle-filter').addEventListener('click', () => {
            document.getElementById('filter-panel').classList.toggle('hidden');
        });

        (() => {
            const modal = document.getElementById('photo-modal');
            const label = document.getElementById('photo-modal-label');
            const body  = document.getElementById('photo-modal-body');
            const close = document.getElementById('close-photo-modal');

            const open = (lbl, photos) => {
                label.textContent = lbl;
                body.innerHTML = '';
                photos.forEach((p, i) => {
                    const div = document.createElement('div');
                    div.className = 'space-y-2';
                    div.innerHTML = `
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-500">Foto ${i + 1}</span>
                            ${p.tanggal ? `<span class="text-xs text-slate-400">${p.tanggal}</span>` : ''}
                        </div>
                        <img src="${p.url}" alt="Foto ${i + 1}" class="w-full rounded-xl border border-slate-200 object-cover" style="max-height: 50vh;">
                    `;
                    body.appendChild(div);
                });
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            };

            document.querySelectorAll('.open-photo-modal').forEach(btn => {
                btn.addEventListener('click', () => {
                    const photos = JSON.parse(btn.dataset.photos);
                    const label2 = btn.dataset.label;
                    open(label2, photos);
                });
            });

            close.addEventListener('click', () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            });

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }
            });
        })();
    </script>
</x-layouts.app>
