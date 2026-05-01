<x-layouts.app :title="'Dashboard'">
    <section class="hero">
        <div class="card">
            <p class="muted">Monitoring operasional</p>
            <h1 class="headline">Pantau logistik multi cabang dari satu dashboard yang ringkas.</h1>
            <p class="muted">Status approval, aktivitas terbaru, dan pergerakan barang masuk versus keluar tersedia dalam satu tampilan.</p>
        </div>
        <div class="card">
            <form method="GET" class="toolbar">
                @if ($isGlobalView)
                    <div>
                        <label for="branch_id">Filter cabang</label>
                        <select id="branch_id" name="branch_id">
                            <option value="">Semua cabang</option>
                            @foreach ($branchOptions as $branch)
                                <option value="{{ $branch->id }}" @selected($selectedBranchId === $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="actions">
                    <button class="button button-primary" type="submit">Terapkan filter</button>
                    <a class="button" href="{{ route('logistics.create') }}">Tambah logistik</a>
                </div>
            </form>
        </div>
    </section>

    <section class="stats">
        <div class="card"><div class="muted">Total Data</div><div class="stat-number">{{ $stats['total'] }}</div></div>
        <div class="card"><div class="muted">Pending</div><div class="stat-number">{{ $stats['pending'] }}</div></div>
        <div class="card"><div class="muted">Approved</div><div class="stat-number">{{ $stats['approved'] }}</div></div>
        <div class="card"><div class="muted">Rejected</div><div class="stat-number">{{ $stats['rejected'] }}</div></div>
    </section>

    <section class="grid grid-2">
        <div class="card">
            <h2>Barang Masuk vs Keluar</h2>
            @php($totalFlow = max($chart['masuk'] + $chart['keluar'], 1))
            <div style="margin:1rem 0;">
                <div style="display:flex;justify-content:space-between;"><span>Masuk</span><strong>{{ $chart['masuk'] }}</strong></div>
                <div class="chart-bar"><span style="width:{{ ($chart['masuk'] / $totalFlow) * 100 }}%"></span></div>
            </div>
            <div>
                <div style="display:flex;justify-content:space-between;"><span>Keluar</span><strong>{{ $chart['keluar'] }}</strong></div>
                <div class="chart-bar"><span style="width:{{ ($chart['keluar'] / $totalFlow) * 100 }}%; background:linear-gradient(90deg,#f59e0b,#f97316);"></span></div>
            </div>
        </div>
        <div class="card">
            <h2>Aktivitas Terbaru</h2>
            @forelse ($recentActivities as $activity)
                <div class="activity-item">
                    <strong>{{ $activity['title'] }}</strong>
                    <div class="muted">{{ $activity['meta'] }}</div>
                    <small class="muted">{{ $activity['time']->format('d M Y H:i') }}</small>
                </div>
            @empty
                <div class="empty">Belum ada aktivitas.</div>
            @endforelse
        </div>
    </section>
</x-layouts.app>
