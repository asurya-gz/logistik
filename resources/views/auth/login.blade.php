<x-layouts.app :title="'Login'">
    <div class="min-h-screen bg-slate-50 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            {{-- Brand --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-slate-900 rounded-xl mb-4">
                    <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/>
                        <path d="M16.5 9.4 7.55 4.24"/>
                        <polyline points="3.29 7 12 12 20.71 7"/>
                        <line x1="12" x2="12" y1="22" y2="12"/>
                        <circle cx="18.5" cy="15.5" r="2.5"/>
                        <path d="M15.77 16.5 17 18l3-2"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">Sistem Logistik</h1>
                <p class="text-sm text-slate-500 mt-1">Manajemen distribusi multi cabang</p>
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">

                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-slate-900">Selamat datang kembali</h2>
                    <p class="text-sm text-slate-500 mt-1">Masuk untuk melanjutkan ke dashboard operasional.</p>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div class="space-y-1.5">
                        <label for="email" class="block text-sm font-medium text-slate-700">
                            Email
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            placeholder="nama@perusahaan.com"
                            required
                            autofocus
                            class="w-full px-3.5 py-2.5 text-sm text-slate-900 bg-white border rounded-lg outline-none transition
                                   placeholder:text-slate-400
                                   {{ $errors->has('email') ? 'border-red-400 focus:ring-2 focus:ring-red-100' : 'border-slate-300 focus:border-slate-400 focus:ring-2 focus:ring-slate-100' }}"
                        >
                        @error('email')
                            <p class="text-xs text-red-500 flex items-center gap-1 mt-1">
                                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="space-y-1.5">
                        <label for="password" class="block text-sm font-medium text-slate-700">
                            Password
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="Masukkan password"
                                required
                                class="w-full px-3.5 py-2.5 pr-11 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition
                                       placeholder:text-slate-400
                                       focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                            >
                            <button
                                type="button"
                                id="toggle-password"
                                class="absolute inset-y-0 right-0 flex items-center justify-center w-11 text-slate-400 hover:text-slate-700 transition-colors"
                                aria-label="Tampilkan password"
                                aria-pressed="false"
                            >
                                <svg id="eye-open-icon" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg id="eye-closed-icon" class="hidden w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m3 3 18 18"/>
                                    <path d="M10.58 10.58a2 2 0 0 0 2.83 2.83"/>
                                    <path d="M9.88 5.09A10.94 10.94 0 0 1 12 4.91c5.11 0 9.27 3.24 10 7.09a10.96 10.96 0 0 1-4.16 5.94"/>
                                    <path d="M6.61 6.61A10.95 10.95 0 0 0 2 12c.73 3.85 4.89 7.09 10 7.09a10.93 10.93 0 0 0 5.17-1.27"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white text-sm font-medium py-2.5 px-4 rounded-lg transition-colors duration-150 cursor-pointer"
                    >
                        Masuk ke Dashboard
                    </button>
                </form>
            </div>

            {{-- Demo accounts --}}
            <div class="mt-4 bg-white rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Akun Demo</p>
                <div class="space-y-2">
                    @foreach ([
                        ['role' => 'M. Kantor',      'email' => 'kantor@logistik.test'],
                        ['role' => 'Officer / M. Logistik', 'email' => 'logistik.jakarta@logistik.test'],
                        ['role' => 'M. Lapangan',    'email' => 'lapangan.jakarta@logistik.test'],
                    ] as $account)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 w-28 shrink-0">{{ $account['role'] }}</span>
                            <code class="text-slate-700 bg-slate-50 border border-slate-200 rounded px-2 py-0.5 font-mono truncate">
                                {{ $account['email'] }}
                            </code>
                            <code class="text-slate-500 bg-slate-50 border border-slate-200 rounded px-2 py-0.5 font-mono shrink-0 ml-1.5">
                                password
                            </code>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const passwordInput = document.getElementById('password');
            const togglePasswordButton = document.getElementById('toggle-password');
            const eyeOpenIcon = document.getElementById('eye-open-icon');
            const eyeClosedIcon = document.getElementById('eye-closed-icon');

            if (!passwordInput || !togglePasswordButton || !eyeOpenIcon || !eyeClosedIcon) {
                return;
            }

            togglePasswordButton.addEventListener('click', () => {
                const isHidden = passwordInput.type === 'password';

                passwordInput.type = isHidden ? 'text' : 'password';
                togglePasswordButton.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
                togglePasswordButton.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
                eyeOpenIcon.classList.toggle('hidden', isHidden);
                eyeClosedIcon.classList.toggle('hidden', !isHidden);
            });
        });
    </script>
</x-layouts.app>
