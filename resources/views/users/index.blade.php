<x-layouts.app :title="'User'">
    <div class="space-y-4 p-3 sm:p-5">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">Manajemen User</h1>
                <p class="text-xs text-slate-400 mt-0.5">Kelola akun M. Kantor, Officer, dan M. Lapangan</p>
            </div>
            <a href="{{ route('superadmin.users.create') }}"
                class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-medium text-white hover:bg-slate-700 transition-colors" style="color:white">
                + Tambah User
            </a>
        </div>

        {{-- List --}}
        <div class="space-y-2">
            @forelse ($users as $user)
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="flex flex-wrap items-start justify-between gap-4 px-5 py-4">

                        {{-- Info --}}
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-500">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-medium text-slate-500">
                                        {{ \App\Models\User::roleOptions()[$user->role] ?? $user->role }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ $user->email ?: 'Tanpa email' }}
                                    @if ($user->branch)
                                        <span class="text-slate-200 mx-1">&middot;</span>
                                        {{ $user->branch->name }}
                                    @endif
                                </p>
                                @if ($user->identity_number)
                                    <p class="mt-1 font-mono text-xs text-slate-400">{{ $user->identity_number }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 shrink-0">
                            @if ($barcode->canRender($user->identity_number))
                                <a href="{{ route('superadmin.users.barcode', $user) }}"
                                    class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                                    QR Code
                                </a>
                            @endif
                            <a href="{{ route('superadmin.users.edit', $user) }}"
                                class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus user ini?')"
                                    class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- QR preview --}}
                    @if ($barcode->canRender($user->identity_number))
                        <div class="border-t border-slate-100 px-5 py-3 bg-slate-50/40 flex items-center gap-3">
                            <div class="rounded-lg border border-slate-200 bg-white p-1.5 shrink-0">
                                {!! $barcode->svg($user->identity_number, 48, 4) !!}
                            </div>
                            <p class="text-xs text-slate-400">Scan QR untuk identifikasi lapangan</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white py-16 text-center">
                    <p class="text-sm text-slate-400">Belum ada user.</p>
                </div>
            @endforelse
        </div>

        <div class="flex justify-end">{{ $users->links() }}</div>
    </div>
</x-layouts.app>
