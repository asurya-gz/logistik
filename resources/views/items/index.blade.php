<x-layouts.app :title="'Master Barang'">
    <div class="space-y-4 p-3 sm:p-5">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">Master Barang</h1>
                <p class="text-xs text-slate-400 mt-0.5">Kelola daftar barang untuk transaksi logistik</p>
            </div>
            <a href="{{ route('superadmin.items.create') }}"
                class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-medium text-white hover:bg-slate-700 transition-colors"
                style="color:white">
                + Tambah
            </a>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="table-wrap">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/60">
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400">Kode</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400">Nama Barang</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400">Status</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400">Deskripsi</th>
                        <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wide text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3 font-mono text-xs text-slate-500">{{ $item->code }}</td>
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $item->name }}</td>
                            <td class="px-5 py-3">
                                @if ($item->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Aktif</span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 border border-slate-200 px-2.5 py-0.5 text-xs font-medium text-slate-500">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-xs text-slate-400 max-w-xs truncate">{{ $item->description ?: '—' }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('superadmin.items.edit', $item) }}"
                                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('superadmin.items.destroy', $item) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus barang ini?')"
                                            class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-14 text-center text-sm text-slate-400">Belum ada barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        <div class="flex justify-end">{{ $items->links() }}</div>
    </div>
</x-layouts.app>
