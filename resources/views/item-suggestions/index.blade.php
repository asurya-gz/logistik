<x-layouts.app :title="'Usulan Barang'">
    <div class="space-y-4 p-3 sm:p-5">

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">Usulan Barang</h1>
                <p class="text-xs text-slate-400 mt-0.5">
                    @if (auth()->user()->canManageItems())
                        Daftar usulan nama barang dari manajemen logistik
                    @else
                        Usulkan nama barang yang belum tersedia di master data
                    @endif
                </p>
            </div>
            @if (auth()->user()->canSuggestItems())
                <a href="{{ route('admin.item-suggestions.create') }}"
                    class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-medium text-white hover:bg-slate-700 transition-colors">
                    + Usulkan Barang
                </a>
            @endif
        </div>

        {{-- Tab filter untuk kantor --}}
        @if (auth()->user()->canManageItems())
            <div class="flex rounded-xl border border-slate-200 bg-slate-100 p-0.5 w-fit">
                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}"
                    class="rounded-lg px-3 py-1.5 text-xs font-medium transition
                           {{ !request('status') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    Semua
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}"
                    class="rounded-lg px-3 py-1.5 text-xs font-medium transition
                           {{ request('status') === 'pending' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    Pending
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}"
                    class="rounded-lg px-3 py-1.5 text-xs font-medium transition
                           {{ request('status') === 'approved' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    Disetujui
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}"
                    class="rounded-lg px-3 py-1.5 text-xs font-medium transition
                           {{ request('status') === 'rejected' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    Ditolak
                </a>
            </div>
        @endif

        {{-- List --}}
        <div class="space-y-2">
            @forelse ($suggestions as $suggestion)
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="flex flex-wrap items-start justify-between gap-3 px-5 py-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-semibold text-slate-900">{{ $suggestion->name }}</p>
                                @php
                                    $statusClass = match($suggestion->status) {
                                        'approved' => 'bg-emerald-50 border-emerald-100 text-emerald-700',
                                        'rejected' => 'bg-red-50 border-red-100 text-red-600',
                                        default => 'bg-amber-50 border-amber-100 text-amber-600',
                                    };
                                    $statusLabel = match($suggestion->status) {
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        default => 'Pending',
                                    };
                                @endphp
                                <span class="rounded-full border px-2.5 py-0.5 text-[11px] font-medium {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">
                                {{ $suggestion->suggester->name }}
                                &middot; {{ $suggestion->branch->name }}
                                &middot; {{ $suggestion->created_at->format('d M Y, H:i') }}
                            </p>
                            @if ($suggestion->notes)
                                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed max-w-xl">{{ $suggestion->notes }}</p>
                            @endif
                            @if ($suggestion->office_notes && auth()->user()->canManageItems())
                                <div class="mt-2 rounded-xl bg-slate-50 border border-slate-100 px-3 py-2">
                                    <p class="text-[11px] font-medium text-slate-400 mb-0.5">Catatan Kantor</p>
                                    <p class="text-xs text-slate-600">{{ $suggestion->office_notes }}</p>
                                </div>
                            @endif
                            @if ($suggestion->reviewer)
                                <p class="text-xs text-slate-400 mt-1.5">
                                    Direview oleh {{ $suggestion->reviewer->name }}
                                    &middot; {{ $suggestion->reviewed_at->format('d M Y, H:i') }}
                                </p>
                            @endif
                        </div>

                        @if (auth()->user()->canManageItems() && $suggestion->isPending())
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button"
                                    onclick="document.getElementById('approve-modal-{{ $suggestion->id }}').classList.remove('hidden'); document.getElementById('approve-modal-{{ $suggestion->id }}').classList.add('flex')"
                                    class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition">
                                    Setujui
                                </button>
                                <button type="button"
                                    onclick="document.getElementById('reject-modal-{{ $suggestion->id }}').classList.remove('hidden'); document.getElementById('reject-modal-{{ $suggestion->id }}').classList.add('flex')"
                                    class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-100 transition">
                                    Tolak
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Approve modal --}}
                @if (auth()->user()->canManageItems() && $suggestion->isPending())
                    <div id="approve-modal-{{ $suggestion->id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/45 px-4">
                        <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_24px_80px_rgba(15,23,42,.18)]">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                </svg>
                            </div>
                            <h2 class="mt-4 text-lg font-semibold text-slate-900">Setujui Usulan Barang</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Barang <strong>{{ $suggestion->name }}</strong> akan ditambahkan ke master barang.
                            </p>

                            <form method="POST" action="{{ route('superadmin.item-suggestions.approve', $suggestion) }}" class="mt-4 space-y-3">
                                @csrf
                                <div>
                                    <label for="code-{{ $suggestion->id }}" class="block text-xs font-medium text-slate-600 mb-1">Kode Barang</label>
                                    <input id="code-{{ $suggestion->id }}" name="code" required
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                                        placeholder="Contoh: BRG-001">
                                </div>
                                <div>
                                    <label for="notes-{{ $suggestion->id }}" class="block text-xs font-medium text-slate-600 mb-1">Catatan <span class="text-slate-400 font-normal">(opsional)</span></label>
                                    <textarea id="notes-{{ $suggestion->id }}" name="office_notes" rows="2"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 outline-none transition resize-none focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                                        placeholder="Catatan dari kantor..."></textarea>
                                </div>
                                <div class="flex items-center justify-end gap-3 pt-2">
                                    <button type="button"
                                        onclick="document.getElementById('approve-modal-{{ $suggestion->id }}').classList.add('hidden'); document.getElementById('approve-modal-{{ $suggestion->id }}').classList.remove('flex')"
                                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition">
                                        Setujui & Tambahkan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Reject modal --}}
                    <div id="reject-modal-{{ $suggestion->id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/45 px-4">
                        <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_24px_80px_rgba(15,23,42,.18)]">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            </div>
                            <h2 class="mt-4 text-lg font-semibold text-slate-900">Tolak Usulan Barang</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Usulan <strong>{{ $suggestion->name }}</strong> akan ditolak.
                            </p>

                            <form method="POST" action="{{ route('superadmin.item-suggestions.reject', $suggestion) }}" class="mt-4 space-y-3">
                                @csrf
                                <div>
                                    <label for="reject-notes-{{ $suggestion->id }}" class="block text-xs font-medium text-slate-600 mb-1">Alasan <span class="text-slate-400 font-normal">(opsional)</span></label>
                                    <textarea id="reject-notes-{{ $suggestion->id }}" name="office_notes" rows="2"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 outline-none transition resize-none focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100"
                                        placeholder="Alasan penolakan..."></textarea>
                                </div>
                                <div class="flex items-center justify-end gap-3 pt-2">
                                    <button type="button"
                                        onclick="document.getElementById('reject-modal-{{ $suggestion->id }}').classList.add('hidden'); document.getElementById('reject-modal-{{ $suggestion->id }}').classList.remove('flex')"
                                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition">
                                        Tolak
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Modal backdrop click handler --}}
                    <script>
                        (function() {
                            const approveModal = document.getElementById('approve-modal-{{ $suggestion->id }}');
                            const rejectModal = document.getElementById('reject-modal-{{ $suggestion->id }}');
                            [approveModal, rejectModal].forEach(function(modal) {
                                if (!modal) return;
                                modal.addEventListener('click', function(e) {
                                    if (e.target === modal) {
                                        modal.classList.add('hidden');
                                        modal.classList.remove('flex');
                                    }
                                });
                            });
                        })();
                    </script>
                @endif
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white py-16 text-center">
                    <p class="text-sm text-slate-400">Belum ada usulan barang.</p>
                    @if (auth()->user()->canSuggestItems())
                        <a href="{{ route('admin.item-suggestions.create') }}"
                            class="inline-block mt-3 rounded-xl bg-slate-900 px-4 py-2 text-xs font-medium text-white hover:bg-slate-700 transition-colors">
                            + Usulkan Barang
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        <div class="flex justify-end">{{ $suggestions->links() }}</div>
    </div>

    {{-- Keyboard escape for modals --}}
    <script>
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id^="approve-modal-"], [id^="reject-modal-"]').forEach(function(modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                });
            }
        });
    </script>
</x-layouts.app>
