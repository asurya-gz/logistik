<x-layouts.app :title="'Login'">
    <div class="min-h-screen flex">

        {{-- Left Panel --}}
        <div class="hidden lg:flex lg:w-1/2 relative bg-slate-900 flex-col justify-between p-12 overflow-hidden">
            {{-- Subtle grid pattern --}}
            <div class="absolute inset-0 opacity-[0.03]"
                 style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 40px 40px;"></div>

            {{-- Glow --}}
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-600 rounded-full opacity-10 blur-3xl"></div>
            <div class="absolute -bottom-32 -right-16 w-80 h-80 bg-indigo-600 rounded-full opacity-10 blur-3xl"></div>

            {{-- Logo --}}
            <div class="relative flex items-center gap-3">
                <div class="w-9 h-9 bg-white/10 backdrop-blur rounded-lg flex items-center justify-center border border-white/10">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/>
                        <path d="M16.5 9.4 7.55 4.24"/>
                        <polyline points="3.29 7 12 12 20.71 7"/>
                        <line x1="12" x2="12" y1="22" y2="12"/>
                        <circle cx="18.5" cy="15.5" r="2.5"/>
                        <path d="M15.77 16.5 17 18l3-2"/>
                    </svg>
                </div>
                <span class="text-white font-medium text-sm tracking-wide">Sistem Logistik</span>
            </div>

            {{-- Center content --}}
            <div class="relative">
                <div class="inline-flex items-center gap-2 bg-white/5 border border-white/10 rounded-full px-3.5 py-1.5 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs text-white/60 font-medium tracking-wide">Sistem aktif</span>
                </div>
                <h2 class="text-3xl font-semibold text-white leading-snug mb-4">
                    Manajemen distribusi<br>multi cabang
                </h2>
                <p class="text-sm text-white/40 leading-relaxed max-w-xs">
                    Platform terpadu untuk monitoring logistik, verifikasi pengiriman, dan pelaporan lapangan secara real-time.
                </p>
            </div>

            {{-- Footer --}}
            <div class="relative flex items-center gap-2 text-xs text-white/25">
                <span>&copy; {{ date('Y') }} Sistem Logistik.</span>
                <span>&middot;</span>
                <span>Semua hak dilindungi.</span>
            </div>
        </div>

        {{-- Right Panel --}}
        <div class="flex-1 flex items-center justify-center px-6 py-12 bg-white">
            <div class="w-full max-w-sm">

                {{-- Mobile logo --}}
                <div class="lg:hidden flex items-center gap-2.5 mb-10">
                    <div class="w-8 h-8 bg-slate-900 rounded-lg flex items-center justify-center">
                        <svg class="w-4.5 h-4.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/>
                            <path d="M16.5 9.4 7.55 4.24"/>
                            <polyline points="3.29 7 12 12 20.71 7"/>
                            <line x1="12" x2="12" y1="22" y2="12"/>
                        </svg>
                    </div>
                    <span class="text-slate-900 font-semibold text-sm">Sistem Logistik</span>
                </div>

                {{-- Header --}}
                <div class="mb-8">
                    <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">Selamat datang</h1>
                    <p class="text-sm text-slate-400 mt-1.5">Masuk untuk melanjutkan ke dashboard.</p>
                </div>

                {{-- Form --}}
                <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            placeholder="nama@contoh.com"
                            required
                            autofocus
                            class="w-full px-4 py-2.5 text-sm text-slate-900 bg-slate-50 border rounded-xl outline-none transition-all duration-200
                                   placeholder:text-slate-300
                                   {{ $errors->has('email') ? 'border-red-300 bg-red-50 focus:border-red-400 focus:ring-3 focus:ring-red-50' : 'border-slate-200 focus:border-slate-400 focus:bg-white focus:ring-3 focus:ring-slate-100' }}"
                        >
                        @error('email')
                            <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                                required
                                class="w-full px-4 py-2.5 pr-11 text-sm text-slate-900 bg-slate-50 border border-slate-200 rounded-xl outline-none transition-all duration-200
                                       placeholder:text-slate-300
                                       focus:border-slate-400 focus:bg-white focus:ring-3 focus:ring-slate-100"
                            >
                            <button
                                type="button"
                                id="toggle-password"
                                class="absolute inset-y-0 right-0 flex items-center justify-center w-11 text-slate-300 hover:text-slate-600 transition-colors duration-150"
                                aria-label="Tampilkan password"
                                aria-pressed="false"
                            >
                                <svg id="eye-open-icon" class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg id="eye-closed-icon" class="hidden w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                        class="w-full bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white text-sm font-medium py-2.5 px-4 rounded-xl transition-all duration-150 cursor-pointer mt-1"
                    >
                        Masuk
                    </button>
                </form>

                {{-- Field officer note --}}
                <p class="mt-6 text-sm text-slate-400 text-center">
                    Petugas lapangan?
                    <a href="{{ route('field-reports.identity') }}" class="text-slate-700 font-medium hover:text-slate-900 transition-colors duration-150">
                        Akses via nomor identitas
                    </a>
                </p>

                {{-- Demo accounts --}}
                <div class="mt-8 pt-6 border-t border-slate-100">
                    <p class="text-xs text-slate-400 font-medium mb-3">Akun demo</p>
                    <div class="space-y-2">
                        @foreach ([
                            ['role' => 'M. Kantor',      'email' => 'kantor@logistik.test'],
                            ['role' => 'Officer / M. Logistik', 'email' => 'logistik.jakarta@logistik.test'],
                        ] as $account)
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-xs text-slate-400 shrink-0 w-24">{{ $account['role'] }}</span>
                                <code class="text-xs text-slate-600 bg-slate-50 border border-slate-100 rounded-lg px-2 py-1 font-mono truncate flex-1 text-center">{{ $account['email'] }}</code>
                                <code class="text-xs text-slate-400 bg-slate-50 border border-slate-100 rounded-lg px-2 py-1 font-mono shrink-0">password</code>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs text-slate-400">
                        Demo lapangan: <code class="text-slate-600 bg-slate-50 border border-slate-100 rounded px-1.5 py-0.5 font-mono">LPG-JKT-001</code>
                    </p>
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

            if (!passwordInput || !togglePasswordButton) return;

            togglePasswordButton.addEventListener('click', () => {
                const isHidden = passwordInput.type === 'password';
                passwordInput.type = isHidden ? 'text' : 'password';
                togglePasswordButton.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
                togglePasswordButton.setAttribute('aria-pressed', String(isHidden));
                eyeOpenIcon.classList.toggle('hidden', isHidden);
                eyeClosedIcon.classList.toggle('hidden', !isHidden);
            });
        });
    </script>
</x-layouts.app>
