<x-layouts.app :title="'Cabang'">
    <div class="space-y-4 p-3 sm:p-5">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">Manajemen Cabang</h1>
                <p class="text-xs text-slate-400 mt-0.5">Kelola cabang yang terdaftar dalam sistem</p>
            </div>
            <a href="{{ route('superadmin.branches.create') }}"
                class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-medium text-white hover:bg-slate-700 transition-colors" style="color:white">
                + Tambah
            </a>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="table-wrap">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/60">
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400">Nama</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400">Kode</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400">Alamat</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold uppercase tracking-wide text-slate-400">User</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold uppercase tracking-wide text-slate-400">Logistik</th>
                        <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wide text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($branches as $branch)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $branch->name }}</td>
                            <td class="px-5 py-3 font-mono text-xs text-slate-500">{{ $branch->code }}</td>
                            <td class="px-5 py-3 text-xs text-slate-400 max-w-xs truncate">{{ $branch->address ?: '—' }}</td>
                            <td class="px-5 py-3 text-center text-xs font-semibold text-slate-700">{{ $branch->users_count }}</td>
                            <td class="px-5 py-3 text-center text-xs font-semibold text-slate-700">{{ $branch->logistics_count }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('superadmin.branches.edit', $branch) }}"
                                        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('superadmin.branches.destroy', $branch) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus cabang ini?')"
                                            class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-14 text-center text-sm text-slate-400">Belum ada cabang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        <div class="flex justify-end">{{ $branches->links() }}</div>
    </div>
</x-layouts.app>
