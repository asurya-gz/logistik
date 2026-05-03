<x-layouts.app :title="'Dashboard'">
    @php
        $formatCurrency = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
        $totalMovementValue = max($movementChart['incomingValue'] + $movementChart['outgoingValue'], 1);
        $summaryCards = [
            ['label' => 'Total Transaksi', 'value' => number_format($stats['total']), 'hint' => 'data operasional', 'tone' => 'slate'],
            ['label' => 'Nilai Total', 'value' => $formatCurrency($stats['totalValue']), 'hint' => 'akumulasi transaksi', 'tone' => 'cyan'],
            ['label' => 'Barang Masuk', 'value' => $formatCurrency($stats['incomingValue']), 'hint' => number_format($movementChart['incomingQty']) . ' unit', 'tone' => 'emerald'],
            ['label' => 'Barang Keluar', 'value' => $formatCurrency($stats['outgoingValue']), 'hint' => number_format($movementChart['outgoingQty']) . ' unit', 'tone' => 'amber'],
        ];
        $statusCards = [
            ['label' => 'Pending', 'value' => $stats['pending'], 'class' => 'bg-amber-50 text-amber-700 border-amber-100'],
            ['label' => 'Approved', 'value' => $stats['approved'], 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-100'],
            ['label' => 'Rejected', 'value' => $stats['rejected'], 'class' => 'bg-red-50 text-red-700 border-red-100'],
        ];
        $toneClasses = [
            'slate' => ['dot' => 'bg-slate-900', 'soft' => 'bg-slate-100', 'text' => 'text-slate-900'],
            'cyan' => ['dot' => 'bg-cyan-600', 'soft' => 'bg-cyan-50', 'text' => 'text-cyan-700'],
            'emerald' => ['dot' => 'bg-emerald-500', 'soft' => 'bg-emerald-50', 'text' => 'text-emerald-700'],
            'amber' => ['dot' => 'bg-amber-400', 'soft' => 'bg-amber-50', 'text' => 'text-amber-700'],
        ];
    @endphp

    <div class="space-y-4 p-3 sm:p-5">
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                <div class="max-w-2xl">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Monitoring Operasional</p>
                    <h1 class="mt-2 text-xl font-semibold leading-tight text-slate-900 sm:text-2xl">
                        Ringkasan transaksi, nilai barang, dan performa cabang.
                    </h1>
                    <p class="mt-2 max-w-xl text-sm leading-6 text-slate-500">
                        Tampilan dipadatkan agar angka penting, distribusi nilai, dan aktivitas terbaru lebih cepat dibaca saat operasional harian.
                    </p>
                </div>

                <div class="w-full xl:max-w-3xl">
                    <form method="GET" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        @if ($isGlobalView)
                            <div class="space-y-1">
                                <label for="branch_id" class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Cabang</label>
                                <select
                                    id="branch_id"
                                    name="branch_id"
                                    class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                                >
                                    <option value="">Semua cabang</option>
                                    @foreach ($branchOptions as $branch)
                                        <option value="{{ $branch->id }}" @selected($selectedBranchId === $branch->id)>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="space-y-1">
                            <label for="date_from" class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Mulai</label>
                            <input
                                id="date_from"
                                name="date_from"
                                type="date"
                                value="{{ $dateFrom }}"
                                class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                            >
                        </div>
                        <div class="space-y-1">
                            <label for="date_to" class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Akhir</label>
                            <input
                                id="date_to"
                                name="date_to"
                                type="date"
                                value="{{ $dateTo }}"
                                class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                            >
                        </div>
                        <div class="grid grid-cols-2 gap-2 self-end">
                            <button
                                type="submit"
                                class="rounded-xl bg-slate-900 px-3 py-2.5 text-sm font-medium text-white transition-colors hover:bg-slate-800"
                            >
                                Terapkan
                            </button>
                            <a
                                href="{{ route(auth()->user()->panelRouteName('logistics.create')) }}"
                                class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-center text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50"
                            >
                                + Transaksi
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($summaryCards as $card)
                @php($tone = $toneClasses[$card['tone']])
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-medium text-slate-500">{{ $card['label'] }}</p>
                            <p class="mt-2 text-xl font-semibold tracking-tight {{ $tone['text'] }}">{{ $card['value'] }}</p>
                        </div>
                        <span class="mt-1 inline-flex h-2.5 w-2.5 rounded-full {{ $tone['dot'] }} ring-4 {{ $tone['soft'] }}"></span>
                    </div>
                    <p class="mt-2 text-xs text-slate-400">{{ $card['hint'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="grid grid-cols-1 gap-3 lg:grid-cols-[1.2fr_.8fr]">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Distribusi Nilai</h2>
                        <p class="text-xs text-slate-500">Perbandingan nilai barang masuk dan keluar.</p>
                    </div>
                    <div class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-medium text-slate-600">
                        {{ $formatCurrency($movementChart['incomingValue'] + $movementChart['outgoingValue']) }}
                    </div>
                </div>

                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50/70 p-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-emerald-700">Masuk</span>
                            <span class="font-semibold text-emerald-900">{{ $formatCurrency($movementChart['incomingValue']) }}</span>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-emerald-100">
                            <div class="h-full rounded-full bg-emerald-500" style="width: {{ ($movementChart['incomingValue'] / $totalMovementValue) * 100 }}%"></div>
                        </div>
                        <p class="mt-2 text-xs text-emerald-700/80">{{ number_format($movementChart['incomingQty']) }} unit</p>
                    </div>

                    <div class="rounded-xl border border-amber-100 bg-amber-50/70 p-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-amber-700">Keluar</span>
                            <span class="font-semibold text-amber-900">{{ $formatCurrency($movementChart['outgoingValue']) }}</span>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-amber-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-orange-500" style="width: {{ ($movementChart['outgoingValue'] / $totalMovementValue) * 100 }}%"></div>
                        </div>
                        <p class="mt-2 text-xs text-amber-700/80">{{ number_format($movementChart['outgoingQty']) }} unit</p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2">
                    @foreach ($statusCards as $status)
                        <div class="rounded-xl border p-3 {{ $status['class'] }}">
                            <p class="text-[11px] font-medium uppercase tracking-wide">{{ $status['label'] }}</p>
                            <p class="mt-1 text-lg font-semibold">{{ number_format($status['value']) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Aktivitas Terbaru</h2>
                        <p class="text-xs text-slate-500">Update transaksi dan upload terakhir.</p>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    @forelse ($recentActivities as $activity)
                        <div class="flex items-start gap-3 rounded-xl px-1 py-2.5">
                            <div class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-slate-300"></div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-slate-800">{{ $activity['title'] }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $activity['meta'] }}</p>
                                <p class="mt-1 text-[11px] text-slate-400">{{ $activity['time']->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="empty">Belum ada aktivitas.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-3 xl:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Barang Paling Sering Bergerak</h2>
                        <p class="text-xs text-slate-500">Diurutkan berdasarkan quantity tertinggi.</p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-slate-100">
                    @forelse ($topItems as $item)
                        <div class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-3 border-b border-slate-100 px-4 py-3 last:border-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-slate-800">{{ $item['name'] }}</p>
                                <p class="text-xs text-slate-500">{{ $item['transactions'] }} transaksi</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-900">{{ number_format($item['quantity']) }} unit</p>
                                <p class="text-xs text-slate-500">{{ $formatCurrency($item['value']) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="empty m-3">Belum ada data barang.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">{{ $isGlobalView ? 'Ringkasan Per Cabang' : 'Ringkasan Cabang Anda' }}</h2>
                        <p class="text-xs text-slate-500">{{ $isGlobalView ? 'Membandingkan performa cabang berdasarkan nilai transaksi.' : 'Menampilkan performa cabang yang terkait dengan akun login.' }}</p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-slate-100">
                    @forelse ($branchSummaries as $summary)
                        <div class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-3 border-b border-slate-100 px-4 py-3 last:border-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-slate-800">{{ $summary['branch'] }}</p>
                                <p class="text-xs text-slate-500">{{ $summary['transactions'] }} transaksi · {{ $summary['pending'] }} pending</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-900">{{ $formatCurrency($summary['value']) }}</p>
                                <p class="text-xs text-slate-500">{{ number_format($summary['quantity']) }} unit</p>
                            </div>
                        </div>
                    @empty
                        <div class="empty m-3">Belum ada ringkasan cabang.</div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
