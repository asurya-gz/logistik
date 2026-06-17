<x-layouts.app :title="'Pengaturan Harga'">
    <div class="space-y-4 p-3 sm:p-5">

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">Pengaturan Harga</h1>
                <p class="text-xs text-slate-400 mt-0.5">Harga global atau per cabang berdasarkan tanggal berlaku</p>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" class="flex flex-wrap items-center gap-1.5 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                    <span class="text-xs text-slate-400 shrink-0">Barang</span>
                    <select name="item_id" class="h-7 rounded-lg border border-slate-200 bg-slate-50 px-2 text-xs text-slate-700 outline-none">
                        <option value="">Semua</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" @selected($selectedItemId === $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <span class="text-slate-200 text-xs">|</span>
                    <span class="text-xs text-slate-400 shrink-0">Cabang</span>
                    <select name="branch_id" class="h-7 rounded-lg border border-slate-200 bg-slate-50 px-2 text-xs text-slate-700 outline-none">
                        <option value="">Semua</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected($selectedBranchId === $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="h-7 rounded-lg bg-slate-900 px-3 text-xs font-medium text-white hover:bg-slate-700 transition-colors">Terapkan</button>
                    @if ($selectedItemId || $selectedBranchId)
                        <a href="{{ route('superadmin.prices.index') }}"
                            class="h-7 flex items-center rounded-lg border border-slate-200 px-2.5 text-xs text-slate-400 hover:bg-slate-50 transition-colors">✕</a>
                    @endif
                </form>
                <a href="{{ route('superadmin.prices.create') }}"
                    class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-medium text-white hover:bg-slate-700 transition-colors" style="color:white">
                    + Set Harga
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="table-wrap">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/60">
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400">Barang</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400">Cabang</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400">Harga</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400">Berlaku</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400">Dibuat</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400">Catatan</th>
                        <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wide text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($prices as $price)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $price->item->name }}</td>
                            <td class="px-5 py-3 text-xs text-slate-500">
                                @if ($price->branch)
                                    {{ $price->branch->name }}
                                @else
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-slate-500">Global</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 font-semibold text-slate-900">Rp {{ number_format((float) $price->price, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-xs text-slate-500">{{ $price->effective_date->format('d M Y') }}</td>
                            <td class="px-5 py-3 text-xs text-slate-400">{{ $price->creator->name }}</td>
                            <td class="px-5 py-3 text-xs text-slate-400 max-w-xs truncate">{{ $price->notes ?: '—' }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('superadmin.prices.edit', $price) }}"
                                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('superadmin.prices.destroy', $price) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus harga ini?')"
                                            class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-14 text-center text-sm text-slate-400">Belum ada histori harga.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        <div class="flex justify-end">{{ $prices->links() }}</div>
    </div>
</x-layouts.app>
