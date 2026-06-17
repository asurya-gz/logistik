<x-layouts.app :title="'Verifikasi'">
    @php
        $canVerify = $user->canVerify();
        $canNote   = $user->canWriteLogistikNote();
        $isOffice  = $user->isFullAccess();
    @endphp

    <div class="space-y-4 p-3 sm:p-5">

        {{-- ═══ Header ═══ --}}
        <div class="flex items-center justify-between gap-4 flex-col sm:flex-row">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">Verifikasi</h1>
                <p class="text-xs text-slate-400 mt-0.5">
                    {{ $isOffice ? 'Tindak lanjut laporan dari semua cabang' : 'Tindak lanjut laporan masuk' }}
                </p>
            </div>
            @if ($isOffice)
                <form method="GET" class="flex items-center gap-1.5 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                    <span class="text-xs text-slate-400 shrink-0">Cabang</span>
                    <select name="branch_id" onchange="this.form.submit()" class="h-7 rounded-lg border border-slate-200 bg-slate-50 px-2 text-xs text-slate-700 outline-none focus:border-indigo-300">
                        <option value="">Semua</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected($selectedBranchId === $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @if ($selectedBranchId)
                        <a href="{{ route(auth()->user()->panelRouteName('verifications.index')) }}"
                            class="h-7 flex items-center rounded-lg border border-slate-200 px-2.5 text-xs text-slate-400 hover:bg-slate-50 transition-colors">✕</a>
                    @endif
                </form>
            @endif
        </div>

        {{-- ═══ Laporan Cards ═══ --}}
        <div class="space-y-3">
            @forelse ($items as $item)
                @php
                    $hasPhotos    = $item->photos->isNotEmpty();
                    $allSet       = $hasPhotos && $item->photos->every(fn($p) => $p->status !== 'none');
                    $incomplete   = $hasPhotos && !$allSet;
                    $totalItems   = $item->photos->sum(fn($p) => $p->items->sum('quantity'));
                @endphp

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

                    {{-- ── Card Header ── --}}
                    <div class="flex items-start justify-between gap-4 px-5 py-4">
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $allSet ? 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100' : ($incomplete ? 'bg-amber-50 text-amber-600 ring-1 ring-amber-100' : 'bg-slate-50 text-slate-400 ring-1 ring-slate-100') }}">
                                @if ($allSet)
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12.75 11.25 15 15.5 9.75"/><path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9Z"/></svg>
                                @elseif ($incomplete)
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                @else
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900">{{ $item->nama_barang }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ $item->branch->name }}
                                    <span class="text-slate-300 mx-1">&middot;</span>
                                    {{ $item->tanggal->format('d M Y') }}
                                    @if ($item->creator)
                                        <span class="text-slate-300 mx-1">&middot;</span>
                                        {{ $item->creator->name }}
                                    @endif
                                    @if ($item->jumlah)
                                        <span class="text-slate-300 mx-1">&middot;</span>
                                        <strong class="text-slate-600">{{ $item->jumlah }} pcs</strong>
                                    @endif
                                </p>
                                @if ($item->keterangan)
                                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed max-w-2xl">{{ $item->keterangan }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if ($canVerify)
                                @if ($incomplete)
                                    <span class="text-[11px] text-amber-600 bg-amber-50 border border-amber-100 rounded-full px-2.5 py-1 font-medium">⚠ Belum lengkap</span>
                                @endif
                                <form method="POST" action="{{ route(auth()->user()->panelRouteName('verifications.update'), $item) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" @disabled(!$allSet)
                                        class="rounded-xl px-4 py-2 text-xs font-semibold transition shadow-sm
                                            {{ $allSet ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-slate-100 text-slate-400 cursor-not-allowed' }}">
                                        ✓ Setujui
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- ── Photos + Detail ── --}}
                    @if ($hasPhotos)
                        <div class="border-t border-slate-100 bg-slate-50/30 px-5 py-4">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $item->photos->count() }} Foto</span>
                                @if ($totalItems > 0)
                                    <span class="text-slate-200">&middot;</span>
                                    <span class="text-[11px] text-indigo-500 font-medium">{{ $totalItems }} barang</span>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach ($item->photos as $photo)
                                    @php
                                        $statusBadge = match($photo->status) {
                                            'ok'     => 'bg-emerald-100 text-emerald-700',
                                            'reject' => 'bg-red-100 text-red-600',
                                            default  => 'bg-amber-100 text-amber-700',
                                        };
                                        $statusDot = match($photo->status) {
                                            'ok'     => 'bg-emerald-500',
                                            'reject' => 'bg-red-500',
                                            default  => 'bg-amber-400 animate-pulse',
                                        };
                                        $statusLabel = \App\Models\LogisticsPhoto::STATUSES[$photo->status] ?? $photo->status;
                                    @endphp

                                    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden shadow-sm">
                                        <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank" rel="noopener" class="block relative">
                                            <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                                alt="Foto {{ $loop->iteration }}"
                                                class="w-full aspect-[4/3] object-cover hover:opacity-90 transition">
                                            <div class="absolute top-2 left-2 flex items-center gap-1.5 rounded-full bg-white/90 backdrop-blur px-2 py-0.5 text-[10px] font-medium text-slate-600 shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
                                                {{ $statusLabel }}
                                            </div>
                                            <span class="absolute top-2 right-2 rounded-full bg-black/40 text-white text-[10px] font-bold px-2 py-0.5 backdrop-blur">#{{ $loop->iteration }}</span>
                                        </a>

                                        <div class="p-2.5 space-y-2">
                                            @if ($canVerify)
                                                <form method="POST"
                                                    action="{{ route(auth()->user()->panelRouteName('verifications.photo-status'), $photo) }}"
                                                    class="flex gap-1.5">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" name="status" value="ok"
                                                        class="flex-1 rounded-lg py-1.5 text-[11px] font-semibold transition border
                                                            {{ $photo->status === 'ok' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-emerald-700 border-emerald-200 hover:bg-emerald-50' }}">
                                                        ✓ OK
                                                    </button>
                                                    <button type="submit" name="status" value="reject"
                                                        class="flex-1 rounded-lg py-1.5 text-[11px] font-semibold transition border
                                                            {{ $photo->status === 'reject' ? 'bg-red-500 text-white border-red-500' : 'bg-white text-red-600 border-red-200 hover:bg-red-50' }}">
                                                        ✕ Tolak
                                                    </button>
                                                </form>
                                            @endif

                                            <button type="button" onclick="toggleDetailBarang({{ $photo->id }})"
                                                class="w-full flex items-center justify-between rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                                                <span class="flex items-center gap-1.5">
                                                    <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1.1-1.79l-6-3a2 2 0 0 0-1.8 0l-6 3A2 2 0 0 0 5 8v8a2 2 0 0 0 1.1 1.79l6 3a2 2 0 0 0 1.8 0l6-3A2 2 0 0 0 21 16Z"/></svg>
                                                    Detail Barang
                                                </span>
                                                <span id="detail-badge-{{ $photo->id }}" class="flex items-center gap-1">
                                                    @if ($photo->items->isNotEmpty())
                                                        <span class="rounded-full bg-indigo-50 px-1.5 text-[10px] font-bold text-indigo-600">{{ $photo->items->sum('quantity') }}</span>
                                                    @endif
                                                    <span id="detail-icon-{{ $photo->id }}" class="text-[10px]">▸</span>
                                                </span>
                                            </button>

                                            <div id="detail-barang-{{ $photo->id }}" class="hidden space-y-2">
                                                @if ($photo->items->isNotEmpty())
                                                    <div class="space-y-1 rounded-lg bg-slate-50 border border-slate-100 p-2">
                                                        @foreach ($photo->items as $pi)
                                                            <div class="flex items-center gap-2 rounded-md bg-white px-3 py-2 border border-slate-100">
                                                                <p class="text-xs font-medium text-slate-700 truncate flex-1 min-w-0">{{ $pi->item->name }}</p>
                                                                <span class="text-xs font-semibold text-slate-500 bg-slate-100 rounded-md px-2 py-0.5 shrink-0">{{ $pi->quantity }} pcs</span>
                                                                <form method="POST" action="{{ route(auth()->user()->panelRouteName('verifications.photo-items.destroy'), $pi) }}">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="text-slate-300 hover:text-red-500 p-0.5 transition-colors" title="Hapus">
                                                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <form method="POST" action="{{ route(auth()->user()->panelRouteName('verifications.photo-items.store'), $photo) }}" class="space-y-2">
                                                    @csrf
                                                    <select name="item_id" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-800 outline-none focus:border-indigo-300 focus:ring-2 focus:ring-indigo-50 transition">
                                                        <option value="">Pilih barang...</option>
                                                        @foreach ($itemList as $it)
                                                            <option value="{{ $it->id }}">{{ $it->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="flex gap-2">
                                                        <div class="flex-1 flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2">
                                                            <span class="text-xs text-slate-400 shrink-0">Jumlah</span>
                                                            <input type="number" name="quantity" min="1" value="1" required
                                                                class="w-full text-right text-xs font-semibold text-slate-800 bg-transparent outline-none">
                                                        </div>
                                                        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-medium text-white hover:bg-slate-700 transition-colors shrink-0">+ Tambah</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ── Catatan Logistik (write) ── --}}
                    @if ($canNote)
                        <div class="border-t border-slate-100 px-5 py-4 bg-slate-50/30">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Catatan Logistik</span>
                            </div>
                            <form method="POST" action="{{ route('admin.verifications.logistik-note', $item) }}" class="space-y-2">
                                @csrf @method('PATCH')
                                <textarea name="logistik_note" rows="2"
                                    placeholder="Tulis catatan untuk kantor..."
                                    class="w-full text-sm bg-white border border-slate-200 rounded-xl px-3 py-2.5 outline-none resize-none transition placeholder:text-slate-300 focus:border-indigo-300 focus:ring-2 focus:ring-indigo-50">{{ $item->logistik_note }}</textarea>
                                <div class="flex items-center justify-between">
                                    @if ($item->logistik_noted_at)
                                        <span class="text-[11px] text-slate-400">Disimpan {{ $item->logistik_noted_at->format('d M Y, H:i') }}</span>
                                    @else
                                        <span></span>
                                    @endif
                                    <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-medium text-white hover:bg-slate-800 transition-colors">Simpan Catatan</button>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- ── Catatan Logistik (read-only) ── --}}
                    @if ($canVerify && $item->logistik_note)
                        <div class="border-t border-slate-100 px-5 py-3.5 bg-slate-50/50">
                            <div class="flex gap-3">
                                <svg class="h-4 w-4 text-slate-300 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-1">Catatan Logistik</p>
                                    <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $item->logistik_note }}</p>
                                    <p class="text-xs text-slate-400 mt-1.5">
                                        {{ $item->logistikNotedBy?->name ?? '—' }}
                                        @if ($item->logistik_noted_at) &middot; {{ $item->logistik_noted_at->format('d M Y, H:i') }} @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ── Foto Pendukung ── --}}
                    <div class="border-t border-slate-100 px-5 py-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Foto Pendukung</span>
                                @if ($item->supportingPhotos->isNotEmpty())
                                    <span class="text-[10px] text-slate-400">({{ $item->supportingPhotos->count() }})</span>
                                @endif
                            </div>
                            <button type="button" onclick="toggleSupportingPhotos({{ $item->id }})"
                                class="text-xs text-indigo-500 hover:text-indigo-700 font-medium transition-colors">
                                {{ $item->supportingPhotos->isEmpty() ? '+ Tambah' : 'Lihat / Tambah' }}
                            </button>
                        </div>

                        {{-- Existing photos --}}
                        @if ($item->supportingPhotos->isNotEmpty())
                            <div id="supp-photos-{{ $item->id }}" class="hidden space-y-3">
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @foreach ($item->supportingPhotos as $sp)
                                        <div class="rounded-lg border border-slate-200 overflow-hidden">
                                            @if ($sp->photo_path)
                                                <a href="{{ asset('storage/' . $sp->photo_path) }}" target="_blank" rel="noopener">
                                                    <img src="{{ asset('storage/' . $sp->photo_path) }}"
                                                        class="w-full aspect-square object-cover hover:opacity-85 transition">
                                                </a>
                                            @endif
                                            <div class="px-2 py-1.5 bg-slate-50 text-[10px] text-slate-400 flex items-center justify-between">
                                                <span>{{ $sp->uploader->name }} · {{ $sp->created_at->format('d/m') }}</span>
                                                <form method="POST" action="{{ route(auth()->user()->panelRouteName('supporting-photos.destroy'), $sp) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-slate-300 hover:text-red-400 transition-colors">✕</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div id="supp-photos-{{ $item->id }}" class="hidden">
                                <p class="text-xs text-slate-400 py-2">Belum ada foto pendukung.</p>
                            </div>
                        @endif

                        {{-- Add form --}}
                        <div id="supp-form-{{ $item->id }}" class="hidden mt-3">
                            <form method="POST" action="{{ route(auth()->user()->panelRouteName('logistics.supporting-photos.store'), $item) }}" enctype="multipart/form-data" class="space-y-2">
                                @csrf
                                <textarea name="supporting_catatan" rows="1" placeholder="Catatan (opsional)..."
                                    class="w-full text-xs bg-white border border-slate-200 rounded-lg px-3 py-2 outline-none resize-none placeholder:text-slate-300 focus:border-indigo-300"></textarea>
                                <div class="flex gap-2">
                                    <input type="file" name="supporting_photos[]" accept="image/*" multiple
                                        class="flex-1 text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-slate-100 file:text-slate-600 hover:file:bg-slate-200 file:transition-colors">
                                    <button type="submit" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-700 transition-colors shrink-0">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- ── Reject ── --}}
                    @if ($canVerify)
                        <div class="border-t border-slate-100 px-5 py-4">
                            <form method="POST" action="{{ route(auth()->user()->panelRouteName('verifications.update'), $item) }}" class="flex gap-2 items-end">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <textarea name="note" rows="1" placeholder="Alasan penolakan..."
                                    class="flex-1 text-sm bg-white border border-slate-200 rounded-xl px-3 py-2.5 outline-none resize-none transition placeholder:text-slate-300 focus:border-red-300 focus:ring-2 focus:ring-red-50"></textarea>
                                <button type="submit"
                                    class="rounded-xl border border-red-200 bg-white px-5 py-2.5 text-xs font-semibold text-red-600 hover:bg-red-50 hover:border-red-300 transition-colors shadow-sm whitespace-nowrap">
                                    ✕ Tolak Laporan
                                </button>
                            </form>
                        </div>
                    @endif

                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-5 py-16 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-300">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12.75 11.25 15 15.5 9.75"/><path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9Z"/></svg>
                    </div>
                    <p class="text-sm font-medium text-slate-500">Semua laporan sudah diverifikasi</p>
                    <p class="text-xs text-slate-400 mt-1">Tidak ada laporan yang perlu ditindaklanjuti saat ini.</p>
                </div>
            @endforelse
        </div>

        <div class="flex justify-end">{{ $items->links() }}</div>
    </div>

    <script>
        function toggleDetailBarang(photoId) {
            const detail = document.getElementById('detail-barang-' + photoId);
            const icon = document.getElementById('detail-icon-' + photoId);
            if (!detail || !icon) return;
            const isHidden = detail.classList.contains('hidden');
            detail.classList.toggle('hidden');
            icon.textContent = isHidden ? '▾' : '▸';
        }

        function toggleSupportingPhotos(itemId) {
            const photos = document.getElementById('supp-photos-' + itemId);
            const form   = document.getElementById('supp-form-' + itemId);
            if (!photos || !form) return;
            const isHidden = photos.classList.contains('hidden');
            photos.classList.toggle('hidden');
            form.classList.toggle('hidden');
        }
    </script>
</x-layouts.app>
