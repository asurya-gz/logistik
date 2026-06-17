<x-layouts.app :title="'Finalisasi Laporan'">
    @php
        $fmt = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
    @endphp

    <div class="space-y-4 p-3 sm:p-5">

        {{-- ═══ Header ═══ --}}
        <div class="flex items-center justify-between gap-3 flex-col sm:flex-row">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">Finalisasi Laporan</h1>
                <p class="text-xs text-slate-400 mt-0.5">Masukkan harga pokok per barang untuk laporan yang telah disetujui</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex rounded-xl border border-slate-200 bg-slate-100 p-0.5">
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'pending']) }}"
                        class="rounded-lg px-3 py-1.5 text-xs font-medium transition
                               {{ $tab === 'pending' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        Belum Finalisasi
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'done']) }}"
                        class="rounded-lg px-3 py-1.5 text-xs font-medium transition
                               {{ $tab === 'done' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        Sudah Finalisasi
                    </a>
                </div>

                @if ($user->isFullAccess())
                    <form method="GET" class="flex items-center gap-1.5 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <span class="text-xs text-slate-400 shrink-0">Cabang</span>
                        <select name="branch_id" onchange="this.form.submit()" class="h-7 rounded-lg border border-slate-200 bg-slate-50 px-2 text-xs text-slate-700 outline-none focus:border-indigo-300">
                            <option value="">Semua</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected($selectedBranchId === $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @if ($selectedBranchId)
                            <a href="{{ request()->fullUrlWithQuery(['branch_id' => null]) }}"
                                class="h-7 flex items-center rounded-lg border border-slate-200 px-2.5 text-xs text-slate-400 hover:bg-slate-50 transition-colors">✕</a>
                        @endif
                    </form>
                @endif
            </div>
        </div>

        {{-- ═══ List ═══ --}}
        <div class="space-y-4">
            @forelse ($items as $item)
                @php
                    // Collect all photo items across all photos
                    $allItems = $item->photos->flatMap(fn ($p) => $p->items->map(fn ($i) => [
                        'photo'    => $p,
                        'item'     => $i->item,
                        'quantity' => $i->quantity,
                        'price'    => $i->price,
                        'id'       => $i->id,
                    ]));
                    $hasItems = $allItems->isNotEmpty();
                    $totalQty = $allItems->sum('quantity');
                @endphp

                <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">

                    {{-- ── Card Header ── --}}
                    <div class="flex items-start justify-between gap-3 px-5 py-4 bg-slate-50/60 border-b border-slate-100">
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $item->isFinalized() ? 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100' : 'bg-purple-50 text-purple-600 ring-1 ring-purple-100' }}">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14.5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900">{{ $item->creator?->name ?? $item->nama_barang }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ $item->branch->name }}
                                    &middot; {{ $item->tanggal->format('d M Y') }}
                                    &middot; {{ $item->photos->count() }} foto
                                    @if ($hasItems) &middot; {{ $allItems->count() }} barang &middot; {{ $totalQty }} pcs @endif
                                </p>
                                @if ($item->keterangan)
                                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">{{ $item->keterangan }}</p>
                                @endif
                            </div>
                        </div>
                        @if ($item->isFinalized())
                            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 shrink-0">✓ Terfinalisasi</span>
                        @elseif (! $hasItems)
                            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-amber-100 border border-amber-200 text-amber-700 shrink-0">⚠ Belum detail barang</span>
                        @endif
                    </div>

                    <div class="p-5">
                        @if ($item->isFinalized())
                            {{-- ── Already Finalized: Per-item breakdown ── --}}
                            <div class="space-y-3">
                                @foreach ($item->photos as $photo)
                                    @if ($photo->items->isNotEmpty())
                                        <div class="rounded-xl border border-slate-200 overflow-hidden">
                                            <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border-b border-slate-100">
                                                <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank" rel="noopener" class="shrink-0">
                                                    <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                                        class="h-8 w-8 rounded-lg object-cover border border-slate-200 hover:opacity-80 transition">
                                                </a>
                                                <span class="text-xs font-medium text-slate-500">Foto {{ $loop->iteration }}</span>
                                            </div>
                                            <div class="divide-y divide-slate-50">
                                                @foreach ($photo->items as $pi)
                                                    <div class="flex items-center justify-between gap-3 px-3 py-2.5">
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-xs font-medium text-slate-700 truncate">{{ $pi->item->name }}</p>
                                                            <p class="text-[11px] text-slate-400">{{ $pi->quantity }} pcs &times; {{ $fmt($pi->price) }}</p>
                                                        </div>
                                                        <span class="text-xs font-bold text-emerald-700 shrink-0">{{ $fmt($pi->price * $pi->quantity) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                                    <p class="text-xs text-slate-400">
                                        Difinalisasi oleh <span class="font-medium text-slate-500">{{ $item->finalizedBy?->name ?? '—' }}</span>
                                        &middot; {{ $item->finalized_at->format('d M Y, H:i') }}
                                    </p>
                                    <p class="text-sm font-bold text-emerald-700">Total: {{ $fmt($item->total_price) }}</p>
                                </div>
                            </div>
                        @elseif ($hasItems)
                            {{-- ── Not finalized: Per-item price form ── --}}
                            <form method="POST"
                                action="{{ route('superadmin.finalisasi.finalize', $item) }}"
                                data-finalize-form>
                                @csrf

                                @php $inputIdx = 0; @endphp
                                <div class="space-y-4">
                                    @foreach ($item->photos as $photo)
                                        @if ($photo->items->isNotEmpty())
                                            <div class="rounded-xl border border-slate-200 overflow-hidden">
                                                <div class="flex items-center gap-2 px-4 py-2.5 bg-slate-50 border-b border-slate-100">
                                                    <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank" rel="noopener" class="shrink-0">
                                                        <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                                            class="h-10 w-10 rounded-lg object-cover border border-slate-200 hover:opacity-90 transition">
                                                    </a>
                                                    <div>
                                                        <span class="text-xs font-medium text-slate-500">Foto {{ $loop->iteration }}</span>
                                                        <span class="text-[11px] text-slate-400 ml-2">{{ $photo->items->count() }} barang</span>
                                                    </div>
                                                </div>
                                                <div class="divide-y divide-slate-50">
                                                    @foreach ($photo->items as $pi)
                                                        <div class="flex items-center gap-3 px-4 py-3">
                                                            <div class="min-w-0 flex-1">
                                                                <p class="text-sm font-medium text-slate-800 truncate">{{ $pi->item->name }}</p>
                                                                <p class="text-[11px] text-slate-400 mt-0.5">Jumlah: <strong class="text-slate-600">{{ $pi->quantity }} pcs</strong></p>
                                                            </div>
                                                            <div class="flex items-center gap-2 shrink-0">
                                                                <span class="text-xs text-slate-400">Rp</span>
                                                                <input type="text" inputmode="numeric"
                                                                    placeholder="0" required data-price-input
                                                                    class="w-32 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-mono text-slate-900 text-right outline-none transition placeholder:text-slate-300 focus:border-purple-300 focus:ring-2 focus:ring-purple-50">
                                                                <input type="hidden" name="item_prices[]" data-price-hidden value="">
                                                                <span class="text-xs text-slate-300">/pcs</span>
                                                            </div>
                                                            <span class="text-xs font-bold text-slate-600 w-24 text-right shrink-0" data-subtotal-display>Rp 0</span>
                                                        </div>
                                                        @php $inputIdx++; @endphp
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="flex items-center justify-between border-t border-slate-100 pt-4 mt-4">
                                    <p class="text-sm font-semibold text-slate-900">
                                        Total: <span class="font-mono text-emerald-700" data-total-display>Rp 0</span>
                                    </p>
                                    <button type="submit"
                                        class="rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-semibold text-white hover:bg-slate-800 transition-colors shadow-sm">
                                        Simpan Finalisasi
                                    </button>
                                </div>
                            </form>
                        @else
                            {{-- ── No items detailed yet ── --}}
                            <div class="text-center py-6">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-400">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                </div>
                                <p class="text-sm font-medium text-slate-500">Belum ada detail barang</p>
                                <p class="text-xs text-slate-400 mt-1">Silakan isi detail barang per foto di halaman <strong>Verifikasi</strong> terlebih dahulu.</p>
                            </div>
                        @endif

                        {{-- Foto Pendukung --}}
                        @if ($item->supportingPhotos->isNotEmpty())
                            <div class="mt-4 pt-4 border-t border-slate-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Foto Pendukung ({{ $item->supportingPhotos->count() }})</span>
                                </div>
                                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                    @foreach ($item->supportingPhotos as $sp)
                                        <div class="rounded-lg border border-slate-200 overflow-hidden">
                                            @if ($sp->photo_path)
                                                <a href="{{ asset('storage/' . $sp->photo_path) }}" target="_blank" rel="noopener">
                                                    <img src="{{ asset('storage/' . $sp->photo_path) }}" class="w-full aspect-square object-cover hover:opacity-85 transition">
                                                </a>
                                            @endif
                                            <div class="px-2 py-1 bg-slate-50 text-[10px] text-slate-400 text-center">
                                                {{ $sp->uploader->name }} &middot; {{ $sp->created_at->format('d/m') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-5 py-16 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-300">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12.75 11.25 15 15.5 9.75"/><path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9Z"/></svg>
                    </div>
                    <p class="text-sm font-medium text-slate-500">
                        {{ $tab === 'done' ? 'Belum ada laporan yang difinalisasi.' : 'Semua laporan sudah difinalisasi.' }}
                    </p>
                </div>
            @endforelse
        </div>

        <div class="flex justify-end">{{ $items->links() }}</div>
    </div>

    <script>
        (() => {
            const fmtDisplay = n => n ? Math.round(n).toLocaleString('id-ID') : '';
            const rawVal     = s => parseFloat(s.replace(/\./g, '').replace(',', '.')) || 0;
            const fmtTotal   = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');

            document.querySelectorAll('[data-finalize-form]').forEach(form => {
                const inputs    = form.querySelectorAll('[data-price-input]');
                const hiddens   = form.querySelectorAll('[data-price-hidden]');
                const subTotals = form.querySelectorAll('[data-subtotal-display]');
                const total     = form.querySelector('[data-total-display]');

                const getQuantities = () => {
                    // Extract quantity from each row's "Jumlah: X pcs" text
                    return [...form.querySelectorAll('.divide-y .flex.items-center.gap-3')].map(row => {
                        const qtyText = row.querySelector('p.text-\\[11px\\]')?.textContent || '';
                        const match = qtyText.match(/(\d+)\s*pcs/);
                        return match ? parseInt(match[1], 10) : 0;
                    });
                };

                const quantities = getQuantities();

                const recalc = () => {
                    let sum = 0;
                    inputs.forEach((el, i) => {
                        const price = rawVal(el.value);
                        const qty = quantities[i] || 0;
                        const subtotal = price * qty;
                        sum += subtotal;
                        if (subTotals[i]) {
                            subTotals[i].textContent = fmtTotal(subtotal);
                        }
                    });
                    total.textContent = fmtTotal(sum);
                };

                inputs.forEach((el, i) => {
                    el.addEventListener('input', () => {
                        const digits = el.value.replace(/\D/g, '');
                        const num    = parseInt(digits, 10) || 0;
                        el.value = num ? fmtDisplay(num) : '';
                        hiddens[i].value = num;
                        recalc();
                    });

                    el.addEventListener('focus', () => {
                        const num = rawVal(el.value);
                        el.value = num ? String(num) : '';
                    });

                    el.addEventListener('blur', () => {
                        const num = rawVal(el.value);
                        el.value = num ? fmtDisplay(num) : '';
                        hiddens[i].value = num;
                    });
                });

                // Initial calc
                recalc();
            });
        })();
    </script>
</x-layouts.app>

