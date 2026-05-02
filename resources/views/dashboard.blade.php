<x-layouts.app :title="'Dashboard'">
    <div class="space-y-6 p-6">

        {{-- Hero --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">

            <div class="max-w-xl">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-2">
                    Monitoring Operasional
                </p>
                <h1 class="text-2xl font-semibold text-slate-900 leading-snug tracking-tight">
                    Pantau logistik multi cabang dari satu dashboard.
                </h1>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                    Status approval, aktivitas terbaru, dan pergerakan barang masuk versus keluar tersedia dalam satu tampilan.
                </p>
            </div>

            {{-- Filter & Actions --}}
            <div class="shrink-0">
                <form method="GET" class="flex flex-wrap items-end gap-3">
                    @if ($isGlobalView)
                        <div class="flex flex-col gap-1">
                            <label for="branch_id" class="text-xs font-medium text-slate-600">
                                Filter cabang
                            </label>
                            <select
                                id="branch_id"
                                name="branch_id"
                                class="text-sm text-slate-800 bg-white border border-slate-300 rounded-lg px-3 py-2 outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100 transition cursor-pointer"
                            >
                                <option value="">Semua cabang</option>
                                @foreach ($branchOptions as $branch)
                                    <option value="{{ $branch->id }}" @selected($selectedBranchId === $branch->id)>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <button
                        type="submit"
                        class="text-sm font-medium text-white bg-slate-900 hover:bg-slate-800 active:bg-slate-950 px-4 py-2 rounded-lg transition-colors cursor-pointer"
                    >
                        Terapkan
                    </button>
                    <a
                        href="{{ route(auth()->user()->panelRouteName('logistics.create')) }}"
                        class="text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 px-4 py-2 rounded-lg transition-colors"
                    >
                        + Tambah Logistik
                    </a>
                </form>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach ([
                ['label' => 'Total Data',  'value' => $stats['total'],    'color' => 'text-slate-900',  'bg' => 'bg-slate-100'],
                ['label' => 'Pending',     'value' => $stats['pending'],  'color' => 'text-amber-600',  'bg' => 'bg-amber-50'],
                ['label' => 'Approved',    'value' => $stats['approved'], 'color' => 'text-emerald-600','bg' => 'bg-emerald-50'],
                ['label' => 'Rejected',    'value' => $stats['rejected'], 'color' => 'text-red-500',    'bg' => 'bg-red-50'],
            ] as $stat)
                <div class="bg-white rounded-2xl border border-slate-200 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-medium text-slate-500">{{ $stat['label'] }}</span>
                        <span class="w-2 h-2 rounded-full {{ $stat['bg'] }} ring-2 ring-offset-1 {{ $stat['color'] === 'text-slate-900' ? 'ring-slate-300' : str_replace('text-', 'ring-', $stat['color']) }}"></span>
                    </div>
                    <p class="text-3xl font-bold tracking-tight {{ $stat['color'] }}">
                        {{ $stat['value'] }}
                    </p>
                </div>
            @endforeach
        </div>

        {{-- Chart + Activity --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            {{-- Barang Masuk vs Keluar --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <h2 class="text-sm font-semibold text-slate-800 mb-5">Barang Masuk vs Keluar</h2>

                @php($totalFlow = max($chart['masuk'] + $chart['keluar'], 1))

                <div class="space-y-5">
                    {{-- Masuk --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-slate-800"></span>
                                <span class="text-slate-600 font-medium">Masuk</span>
                            </div>
                            <strong class="text-slate-900 tabular-nums">{{ $chart['masuk'] }}</strong>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div
                                class="h-full bg-slate-800 rounded-full transition-all duration-500"
                                style="width: {{ ($chart['masuk'] / $totalFlow) * 100 }}%"
                            ></div>
                        </div>
                    </div>

                    {{-- Keluar --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                <span class="text-slate-600 font-medium">Keluar</span>
                            </div>
                            <strong class="text-slate-900 tabular-nums">{{ $chart['keluar'] }}</strong>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div
                                class="h-full rounded-full transition-all duration-500"
                                style="width: {{ ($chart['keluar'] / $totalFlow) * 100 }}%; background: linear-gradient(90deg, #f59e0b, #f97316);"
                            ></div>
                        </div>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="mt-6 pt-5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                    <span>Total pergerakan</span>
                    <span class="font-semibold text-slate-600 tabular-nums">{{ $chart['masuk'] + $chart['keluar'] }} unit</span>
                </div>
            </div>

            {{-- Aktivitas Terbaru --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <h2 class="text-sm font-semibold text-slate-800 mb-5">Aktivitas Terbaru</h2>

                @forelse ($recentActivities as $activity)
                    <div class="flex items-start gap-3 py-3 border-b border-slate-100 last:border-0 last:pb-0 first:pt-0">
                        <div class="mt-0.5 w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0 mt-2"></div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">{{ $activity['title'] }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $activity['meta'] }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $activity['time']->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <svg class="w-8 h-8 text-slate-300 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm text-slate-400">Belum ada aktivitas.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-layouts.app>
