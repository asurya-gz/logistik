<x-layouts.app :title="'Dashboard'">
    @php
        $fmt = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
        $fmtShort = fn ($v) => $v > 0
            ? ($v >= 1_000_000
                ? 'Rp ' . round($v / 1_000_000, 1) . ' jt'
                : 'Rp ' . number_format((float) $v, 0, ',', '.'))
            : '';
        $topItemMax = max($topItems->max('transactions') ?: 1, 1);
        $totalQty = max($movementChart['incomingQty'] + $movementChart['outgoingQty'], 1);
        $inQtyPct = round(($movementChart['incomingQty'] / $totalQty) * 100);
        $outQtyPct = 100 - $inQtyPct;

        // Status icon
        $statusIcon = fn ($s) => match($s) {
            'approved' => '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>',
            'rejected' => '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
            default    => '<svg class="h-3.5 w-3.5 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
        };
    @endphp

    <div class="space-y-4 p-3 sm:p-5">

        {{-- ═══ Topbar ═══ --}}
        <div class="flex items-center justify-between gap-3 flex-col sm:flex-row">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">Dashboard</h1>
                <p class="text-xs text-slate-400 mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <form method="GET" class="flex items-center gap-1.5 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm overflow-x-auto">
                @if ($isGlobalView)
                    <select name="branch_id"
                        class="h-7 max-w-[130px] rounded-lg border border-slate-200 bg-slate-50 px-2 text-xs text-slate-700 outline-none transition focus:border-slate-300 focus:bg-white shrink-0">
                        <option value="">Semua cabang</option>
                        @foreach ($branchOptions as $branch)
                            <option value="{{ $branch->id }}" @selected($selectedBranchId === $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <span class="text-slate-200 text-sm hidden sm:inline">|</span>
                @endif
                <span class="text-xs text-slate-400 shrink-0">dari</span>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                    class="h-7 w-32 rounded-lg border border-slate-200 bg-slate-50 px-2 text-xs text-slate-700 outline-none transition focus:border-slate-300 focus:bg-white shrink-0">
                <span class="text-xs text-slate-400 shrink-0">s/d</span>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                    class="h-7 w-32 rounded-lg border border-slate-200 bg-slate-50 px-2 text-xs text-slate-700 outline-none transition focus:border-slate-300 focus:bg-white shrink-0">
                <button type="submit"
                    class="h-7 rounded-lg bg-slate-900 px-3 text-xs font-medium text-white hover:bg-slate-700 transition-colors shrink-0">
                    Terapkan
                </button>
                @if ($dateFrom || $dateTo || $selectedBranchId)
                    <a href="{{ route(auth()->user()->dashboardRouteName()) }}"
                        class="h-7 flex items-center rounded-lg border border-slate-200 px-2.5 text-xs text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors shrink-0">
                        ✕
                    </a>
                @endif
            </form>
        </div>

        {{-- ═══ 4 KPI Cards ═══ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

            {{-- Hari Ini --}}
            <div class="relative rounded-2xl border border-slate-200 bg-white p-4 shadow-sm overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-blue-50 rounded-bl-[80px] -mr-4 -mt-4"></div>
                <div class="relative">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="14" x2="8" y2="14.01"/><line x1="12" y1="14" x2="12" y2="14.01"/><line x1="16" y1="14" x2="16" y2="14.01"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-[11px] text-slate-400 uppercase tracking-wide font-medium">Hari Ini</p>
                            <p class="text-xl font-bold text-slate-900">{{ number_format($todayCount) }} <span class="text-xs font-normal text-slate-400">laporan</span></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($todayApproved > 0)
                            <span class="text-[11px] text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-full px-2 py-0.5">
                                {!! $statusIcon('approved') !!} {{ $todayApproved }} disetujui
                            </span>
                        @endif
                        @if ($todayPending > 0)
                            <span class="text-[11px] text-amber-600 bg-amber-50 border border-amber-100 rounded-full px-2 py-0.5">
                                {!! $statusIcon('pending') !!} {{ $todayPending }} pending
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Approval Rate --}}
            <div class="relative rounded-2xl border border-slate-200 bg-white p-4 shadow-sm overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-50 rounded-bl-[80px] -mr-4 -mt-4"></div>
                <div class="relative">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-[11px] text-slate-400 uppercase tracking-wide font-medium">Tingkat Persetujuan</p>
                            <p class="text-xl font-bold text-slate-900">{{ $approvalRate }}%</p>
                        </div>
                    </div>
                    <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full bg-emerald-500 transition-all duration-700" style="width: {{ $approvalRate }}%"></div>
                    </div>
                    <p class="mt-1.5 text-[11px] text-slate-400">
                        {{ number_format($stats['approved']) }} dari {{ number_format($stats['approved'] + $stats['rejected']) }} diverifikasi
                    </p>
                </div>
            </div>

            {{-- Total Masuk --}}
            <div class="relative rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-50 rounded-bl-[80px] -mr-4 -mt-4"></div>
                <div class="relative">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-[11px] text-slate-400 uppercase tracking-wide font-medium">Barang Masuk</p>
                            <p class="text-lg font-bold text-emerald-900">{{ number_format($movementChart['incomingQty']) }} <span class="text-xs font-normal text-emerald-600">pcs</span></p>
                        </div>
                    </div>
                    <p class="text-xs text-emerald-600">
                        {{ number_format($movementChart['incomingReports']) }} laporan &middot; {{ $inQtyPct }}% dari total
                        @if ($stats['incomingValue'] > 0)
                            &middot; {{ $fmtShort($stats['incomingValue']) }}
                        @endif
                    </p>
                </div>
            </div>

            {{-- Total Keluar --}}
            <div class="relative rounded-2xl border border-amber-100 bg-white p-4 shadow-sm overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-amber-50 rounded-bl-[80px] -mr-4 -mt-4"></div>
                <div class="relative">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-[11px] text-slate-400 uppercase tracking-wide font-medium">Barang Keluar</p>
                            <p class="text-lg font-bold text-amber-900">{{ number_format($movementChart['outgoingQty']) }} <span class="text-xs font-normal text-amber-600">pcs</span></p>
                        </div>
                    </div>
                    <p class="text-xs text-amber-600">
                        {{ number_format($movementChart['outgoingReports']) }} laporan &middot; {{ $outQtyPct }}% dari total
                        @if ($stats['outgoingValue'] > 0)
                            &middot; {{ $fmtShort($stats['outgoingValue']) }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- KPI Baris 2: Total Barang + Total Rupiah --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="relative rounded-2xl border border-indigo-100 bg-white p-4 shadow-sm overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-indigo-50 rounded-bl-[80px] -mr-4 -mt-4"></div>
                <div class="relative">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 16V8a2 2 0 0 0-1.1-1.79l-6-3a2 2 0 0 0-1.8 0l-6 3A2 2 0 0 0 5 8v8a2 2 0 0 0 1.1 1.79l6 3a2 2 0 0 0 1.8 0l6-3A2 2 0 0 0 21 16Z"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-[11px] text-slate-400 uppercase tracking-wide font-medium">Total Barang</p>
                            <p class="text-xl font-bold text-slate-900">
                                {{ number_format($totalQty) }} <span class="text-xs font-normal text-slate-400">pcs</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-xs">
                        <span class="text-emerald-600">
                            <span class="font-semibold">{{ number_format($movementChart['incomingQty']) }}</span> masuk
                        </span>
                        <span class="text-slate-300">|</span>
                        <span class="text-amber-600">
                            <span class="font-semibold">{{ number_format($movementChart['outgoingQty']) }}</span> keluar
                        </span>
                    </div>
                </div>
            </div>

            <div class="relative rounded-2xl border border-purple-100 bg-white p-4 shadow-sm overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-purple-50 rounded-bl-[80px] -mr-4 -mt-4"></div>
                <div class="relative">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-purple-100 text-purple-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14.5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-[11px] text-slate-400 uppercase tracking-wide font-medium">Total Rupiah <span class="text-purple-400">(Finalisasi)</span></p>
                            <p class="text-xl font-bold text-slate-900">
                                @if ($stats['totalValue'] > 0)
                                    {{ $fmtShort($stats['totalValue']) }}
                                @else
                                    <span class="text-sm font-normal text-slate-300">Belum ada</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400">
                        @if ($stats['totalValue'] > 0)
                            Dari {{ number_format($stats['approved']) }} laporan disetujui
                        @else
                            Menunggu proses finalisasi
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- ═══ Konten Utama: 2 Kolom ═══ --}}
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1fr_360px]">

            {{-- ── KOLOM KIRI ── --}}
            <div class="space-y-4">

                {{-- Perbandingan Masuk vs Keluar --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-3.5">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-slate-900">Perbandingan Barang Masuk vs Keluar</h2>
                            <span class="text-xs text-slate-400">
                                {{ number_format($totalQty) }} pcs total
                            </span>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="flex h-4 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-500 transition-all duration-700" style="width: {{ $inQtyPct }}%"></div>
                            <div class="h-full bg-gradient-to-r from-amber-400 to-amber-500 transition-all duration-700" style="width: {{ $outQtyPct }}%"></div>
                        </div>
                        <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                            <span class="flex items-center gap-1.5 font-medium">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                Masuk <span class="text-slate-400 font-normal">{{ $inQtyPct }}%</span>
                            </span>
                            <span class="flex items-center gap-1.5 font-medium">
                                <span class="text-slate-400 font-normal">{{ $outQtyPct }}%</span> Keluar
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            </span>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-4">
                            <div class="rounded-xl bg-emerald-50/60 border border-emerald-100/60 px-4 py-3.5">
                                <span class="text-[11px] font-semibold uppercase tracking-wide text-emerald-600">Barang Masuk</span>
                                <p class="mt-1.5 text-2xl font-bold text-emerald-900">{{ number_format($movementChart['incomingQty']) }} <span class="text-sm font-normal text-emerald-700">pcs</span></p>
                                <p class="mt-2 text-[11px] text-emerald-600">
                                    {{ number_format($movementChart['incomingReports']) }} laporan
                                    @if ($movementChart['incomingValue'] > 0)
                                        &middot; {{ $fmtShort($movementChart['incomingValue']) }}
                                    @endif
                                </p>
                            </div>
                            <div class="rounded-xl bg-amber-50/60 border border-amber-100/60 px-4 py-3.5">
                                <span class="text-[11px] font-semibold uppercase tracking-wide text-amber-600">Barang Keluar</span>
                                <p class="mt-1.5 text-2xl font-bold text-amber-900">{{ number_format($movementChart['outgoingQty']) }} <span class="text-sm font-normal text-amber-700">pcs</span></p>
                                <p class="mt-2 text-[11px] text-amber-600">
                                    {{ number_format($movementChart['outgoingReports']) }} laporan
                                    @if ($movementChart['outgoingValue'] > 0)
                                        &middot; {{ $fmtShort($movementChart['outgoingValue']) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Perlu Tindakan — Pending Items --}}
                @if ($pendingItems->isNotEmpty())
                    <div class="rounded-2xl border border-amber-200 bg-white shadow-sm overflow-hidden">
                        <div class="border-b border-amber-100 bg-amber-50/70 px-5 py-3.5 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-amber-200 text-amber-700">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                                    </svg>
                                </span>
                                <h2 class="text-sm font-semibold text-amber-800">Perlu Tindakan</h2>
                            </div>
                            <span class="text-xs text-amber-500">{{ number_format($stats['pending']) }} menunggu verifikasi</span>
                        </div>
                        <div class="divide-y divide-slate-50">
                            @foreach ($pendingItems as $pendingItem)
                                <div class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-slate-50/50 transition-colors">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-slate-800 truncate">
                                            {{ $pendingItem->item?->name ?? $pendingItem->nama_barang }}
                                        </p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">
                                            {{ $pendingItem->branch->name }} &middot; {{ $pendingItem->kategori }}
                                            &middot; {{ $pendingItem->jumlah }} pcs
                                            &middot; {{ $pendingItem->tanggal->format('d M') }}
                                        </p>
                                    </div>
                                    <a href="{{ route(auth()->user()->panelRouteName('verifications.index')) }}"
                                        class="shrink-0 rounded-lg bg-amber-100 border border-amber-200 px-3 py-1.5 text-[11px] font-medium text-amber-700 hover:bg-amber-200 transition-colors">
                                        Verifikasi →
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Barang Paling Sering Dilaporkan --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-3.5">
                        <h2 class="text-sm font-semibold text-slate-900">Barang Paling Sering Dilaporkan</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Berdasarkan frekuensi kemunculan di laporan</p>
                    </div>
                    <div class="p-5">
                        @if ($topItems->isNotEmpty())
                            <div class="space-y-4">
                                @foreach ($topItems as $idx => $item)
                                    @php $barPct = round(($item['transactions'] / $topItemMax) * 100); @endphp
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg text-[11px] font-bold
                                                    {{ $idx === 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' }}">
                                                    {{ $idx + 1 }}
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-slate-800 truncate">{{ $item['name'] }}</p>
                                                    <p class="text-[11px] text-slate-400">
                                                        {{ number_format($item['transactions']) }} kali &middot; {{ number_format($item['quantity']) }} pcs
                                                    </p>
                                                </div>
                                            </div>
                                            <span class="text-sm font-semibold text-slate-600 shrink-0">{{ $fmtShort($item['value']) }}</span>
                                        </div>
                                        <div class="h-1.5 overflow-hidden rounded-full bg-slate-100">
                                            <div class="h-full rounded-full {{ $idx === 0 ? 'bg-amber-500' : 'bg-slate-300' }}" style="width: {{ $barPct }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-400 py-4 text-center">Belum ada laporan.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── KOLOM KANAN ── --}}
            <div class="space-y-4">

                {{-- Ringkasan Cepat --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-3.5">
                        <h2 class="text-sm font-semibold text-slate-900">Ringkasan Cepat</h2>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                <span class="text-xs text-slate-600">Total Laporan</span>
                            </div>
                            <span class="text-sm font-bold text-slate-900">{{ number_format($stats['total']) }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <span class="text-xs text-slate-600">Total Foto</span>
                            </div>
                            <span class="text-sm font-bold text-slate-900">{{ number_format($totalPhotos) }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14.5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                <span class="text-xs text-slate-600">Total Barang</span>
                            </div>
                            <span class="text-sm font-bold text-slate-900">{{ number_format($totalQty) }} pcs</span>
                        </div>
                        @if ($stats['totalValue'] > 0)
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <span class="text-xs text-slate-600">Nilai Total</span>
                            </div>
                            <span class="text-sm font-bold text-slate-900">{{ $fmtShort($stats['totalValue']) }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Status Laporan --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-3.5">
                        <h2 class="text-sm font-semibold text-slate-900">Status Laporan</h2>
                    </div>
                    <div class="p-4">
                        @php
                            $totalTx = max($stats['total'], 1);
                            $statusRows = [
                                ['label' => 'Disetujui', 'value' => $stats['approved'], 'color' => 'bg-emerald-500', 'icon' => 'ok', 'pct' => round(($stats['approved'] / $totalTx) * 100)],
                                ['label' => 'Menunggu',  'value' => $stats['pending'],  'color' => 'bg-amber-400',  'icon' => 'pending', 'pct' => round(($stats['pending'] / $totalTx) * 100)],
                                ['label' => 'Ditolak',  'value' => $stats['rejected'], 'color' => 'bg-red-400',    'icon' => 'reject', 'pct' => round(($stats['rejected'] / $totalTx) * 100)],
                            ];
                        @endphp
                        @foreach ($statusRows as $row)
                            <div class="mb-3 last:mb-0">
                                <div class="flex items-center justify-between mb-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full {{ $row['color'] }}"></span>
                                        <span class="text-xs text-slate-700">{{ $row['label'] }}</span>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-500">{{ number_format($row['value']) }} <span class="font-normal">({{ $row['pct'] }}%)</span></span>
                                </div>
                                <div class="h-1.5 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full {{ $row['color'] }} transition-all duration-500" style="width: {{ $row['pct'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Ringkasan Disetujui --}}
                @if ($stats['approved'] > 0)
                    <div class="rounded-2xl border border-emerald-200 bg-white shadow-sm overflow-hidden">
                        <div class="border-b border-emerald-100 bg-emerald-50/70 px-5 py-3.5 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-emerald-200 text-emerald-700">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6 9 17l-5-5"/>
                                    </svg>
                                </span>
                                <h2 class="text-sm font-semibold text-emerald-800">Ringkasan Disetujui</h2>
                            </div>
                            <span class="text-xs text-emerald-500">{{ number_format($stats['approved']) }} laporan</span>
                        </div>
                        <div class="p-4 space-y-2">
                            <div class="flex items-center justify-between rounded-xl bg-emerald-50/50 px-4 py-2.5">
                                <span class="text-xs text-emerald-700">Total laporan disetujui</span>
                                <span class="text-sm font-bold text-emerald-900">{{ number_format($stats['approved']) }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-xl bg-emerald-50/50 px-4 py-2.5">
                                <span class="text-xs text-emerald-700">Total barang (pcs)</span>
                                <span class="text-sm font-bold text-emerald-900">
                                    {{ number_format($movementChart['incomingQty'] + $movementChart['outgoingQty']) }} pcs
                                </span>
                            </div>
                            @if ($stats['totalValue'] > 0)
                            <div class="flex items-center justify-between rounded-xl bg-emerald-50/50 px-4 py-2.5">
                                <span class="text-xs text-emerald-700">Total nilai</span>
                                <span class="text-sm font-bold text-emerald-900">{{ $fmtShort($stats['totalValue']) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Per Cabang --}}
                @if ($branchSummaries->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-3.5">
                            <h2 class="text-sm font-semibold text-slate-900">
                                {{ $isGlobalView ? 'Per Cabang' : 'Ringkasan Cabang' }}
                            </h2>
                        </div>
                        <div class="p-4">
                            @php $branchMax = max($branchSummaries->max('value'), 1); @endphp
                            <div class="space-y-3">
                                @foreach ($branchSummaries as $summary)
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs text-slate-700 truncate max-w-[50%] font-medium">{{ $summary['branch'] }}</span>
                                            <span class="text-xs font-semibold text-slate-800 shrink-0">{{ $fmtShort($summary['value']) }}</span>
                                        </div>
                                        <div class="h-1.5 overflow-hidden rounded-full bg-slate-100">
                                            <div class="h-full rounded-full bg-slate-400" style="width: {{ round(($summary['value'] / $branchMax) * 100) }}%"></div>
                                        </div>
                                        <p class="mt-0.5 text-[11px] text-slate-400">
                                            {{ $summary['transactions'] }} laporan &middot; {{ $summary['pending'] }} pending
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Aktivitas Terbaru --}}
                @if ($recentActivities->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-3.5">
                            <h2 class="text-sm font-semibold text-slate-900">Aktivitas Terbaru</h2>
                        </div>
                        <div class="divide-y divide-slate-50">
                            @foreach ($recentActivities as $activity)
                                <div class="flex items-start gap-3 px-5 py-3 hover:bg-slate-50/30 transition-colors">
                                    <div class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-slate-300"></div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium text-slate-700 truncate">{{ $activity['title'] }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">{{ $activity['meta'] }}</p>
                                        <p class="text-[11px] text-slate-300 mt-0.5">{{ $activity['time']->format('d M, H:i') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-layouts.app>
