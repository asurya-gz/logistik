<x-layouts.app :title="$mode === 'create' ? 'Tambah User' : 'Edit User'">
    <div class="p-3 sm:p-5">
        <div class="mx-auto max-w-xl space-y-4">

            {{-- Header --}}
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">{{ $mode === 'create' ? 'Tambah User' : 'Edit User' }}</h1>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $mode === 'create' ? 'Buat akun baru' : 'Perbarui data akun' }}</p>
                </div>
                <a href="{{ route('superadmin.users.index') }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-medium text-slate-500 hover:bg-slate-50 transition-colors">
                    ← Kembali
                </a>
            </div>

            @php
                $initialIdentity = old('identity_number')
                    ?? $userModel->identity_number
                    ?? $identityGenerator->generate(old('role', $userModel->role ?: \App\Models\User::ROLE_LAPANGAN), (int) old('branch_id', $userModel->branch_id), $mode === 'edit' ? $userModel : null);
            @endphp

            {{-- Form --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="POST"
                    action="{{ $mode === 'create' ? route('superadmin.users.store') : route('superadmin.users.update', $userModel) }}"
                    class="space-y-4">
                    @csrf
                    @if ($mode === 'edit') @method('PUT') @endif

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label for="name" class="block text-xs font-medium text-slate-600">Nama</label>
                            <input id="name" name="name" value="{{ old('name', $userModel->name) }}" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100">
                            @error('name') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1.5" data-email-field>
                            <label for="email" class="block text-xs font-medium text-slate-600">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $userModel->email) }}"
                                {{ old('role', $userModel->role ?: \App\Models\User::ROLE_LAPANGAN) === \App\Models\User::ROLE_LAPANGAN ? '' : 'required' }}
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100">
                            @error('email') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="role" class="block text-xs font-medium text-slate-600">Role</label>
                            <select id="role" name="role" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100">
                                @foreach ($roles as $value => $label)
                                    <option value="{{ $value }}" @selected(old('role', $userModel->role ?: \App\Models\User::ROLE_LAPANGAN) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('role') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="branch_id" class="block text-xs font-medium text-slate-600">Cabang</label>
                            <select id="branch_id" name="branch_id" data-branch-select
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100">
                                <option value="">Tanpa cabang</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected((int) old('branch_id', $userModel->branch_id) === $branch->id)>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            @error('branch_id') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="password" class="block text-xs font-medium text-slate-600">
                                Password {{ $mode === 'edit' ? '<span class="font-normal text-slate-400">(kosongkan jika tidak diubah)</span>' : '' }}
                            </label>
                            <input id="password" name="password" type="password" {{ $mode === 'create' ? 'required' : '' }}
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-100">
                            @error('password') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="identity_number" class="block text-xs font-medium text-slate-600">No. Identitas</label>
                            <input id="identity_number" name="identity_number" value="{{ $initialIdentity }}"
                                readonly data-identity-input
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-mono text-slate-500 outline-none cursor-default">
                            <p class="text-[11px] text-slate-400">Digenerate otomatis dari role & cabang</p>
                            @error('identity_number') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('superadmin.users.index') }}"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-medium text-slate-500 hover:bg-slate-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-medium text-white hover:bg-slate-700 transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>

            {{-- QR Preview (edit mode) --}}
            @if ($mode === 'edit' && $barcode->canRender($userModel->identity_number))
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">QR Code</p>
                            <p class="text-xs text-slate-400 mt-0.5 font-mono">{{ $userModel->identity_number }}</p>
                        </div>
                        <a href="{{ route('superadmin.users.barcode', $userModel) }}"
                            class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors shrink-0">
                            Unduh QR
                        </a>
                    </div>
                    <div class="mt-4 inline-block rounded-xl border border-slate-200 bg-white p-3">
                        {!! $barcode->svg($userModel->identity_number, 160, 10) !!}
                    </div>
                </div>
            @endif

        </div>
    </div>

    <script>
        (() => {
            const roleInput = document.getElementById('role');
            const branchInput = document.querySelector('[data-branch-select]');
            const identityInput = document.querySelector('[data-identity-input]');
            const emailField = document.querySelector('[data-email-field]');
            const emailInput = document.getElementById('email');

            if (!roleInput || !branchInput || !identityInput || !emailField || !emailInput) return;

            const previewUrl = @json(route('superadmin.users.identity-preview'));
            const currentUserId = @json($mode === 'edit' ? $userModel->id : null);
            const lapanganRole = @json(\App\Models\User::ROLE_LAPANGAN);
            let requestId = 0;

            const syncEmailVisibility = () => {
                const isLapangan = roleInput.value === lapanganRole;
                emailField.style.display = isLapangan ? 'none' : '';
                emailInput.required = !isLapangan;
                if (isLapangan) emailInput.value = '';
            };

            const updateIdentityPreview = async () => {
                const params = new URLSearchParams();
                if (roleInput.value) params.set('role', roleInput.value);
                if (branchInput.value) params.set('branch_id', branchInput.value);
                if (currentUserId) params.set('user_id', currentUserId);

                const currentRequestId = ++requestId;
                try {
                    const res = await fetch(`${previewUrl}?${params}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    if (!res.ok || currentRequestId !== requestId) return;
                    const data = await res.json();
                    identityInput.value = data.identity_number ?? '';
                } catch {}
            };

            roleInput.addEventListener('change', () => { syncEmailVisibility(); updateIdentityPreview(); });
            branchInput.addEventListener('change', updateIdentityPreview);
            syncEmailVisibility();
            updateIdentityPreview();
        })();
    </script>
</x-layouts.app>
