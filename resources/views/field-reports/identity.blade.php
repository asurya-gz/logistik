<x-layouts.app :title="'Akses Lapangan'">
    <div class="min-h-screen flex">

        {{-- Left Panel --}}
        <div class="hidden lg:flex lg:w-1/2 relative bg-slate-900 flex-col justify-between p-12 overflow-hidden">
            <div class="absolute inset-0 opacity-[0.03]"
                 style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 40px 40px;"></div>

            <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-600 rounded-full opacity-10 blur-3xl"></div>
            <div class="absolute -bottom-32 -right-16 w-80 h-80 bg-teal-600 rounded-full opacity-10 blur-3xl"></div>

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
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span class="text-xs text-white/60 font-medium tracking-wide">Akses petugas lapangan</span>
                </div>
                <h2 class="text-3xl font-semibold text-white leading-snug mb-4">
                    Verifikasi identitas<br>sebelum melapor
                </h2>
                <p class="text-sm text-white/40 leading-relaxed max-w-xs">
                    Scan QR code pada kartu identitas petugas atau masukkan nomor identitas secara manual untuk mengakses form laporan.
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
                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                    <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">Identitas Lapangan</h1>
                    <p class="text-sm text-slate-400 mt-1.5">Scan QR code atau masukkan nomor identitas.</p>
                </div>

                {{-- Tab switcher --}}
                <div class="flex rounded-xl border border-slate-200 bg-slate-100 p-1 mb-5" role="tablist">
                    <button type="button" role="tab" id="tab-scan" aria-selected="true"
                        class="flex-1 rounded-lg py-2 text-sm font-medium transition tab-btn tab-active"
                        data-tab="scan">
                        Scan QR Code
                    </button>
                    <button type="button" role="tab" id="tab-manual" aria-selected="false"
                        class="flex-1 rounded-lg py-2 text-sm font-medium transition tab-btn"
                        data-tab="manual">
                        Input Manual
                    </button>
                </div>

                <form method="POST" action="{{ route('field-reports.verify') }}" class="space-y-4" data-scan-form>
                    @csrf
                    <input id="identity_number" name="identity_number" type="hidden" value="{{ old('identity_number') }}" required data-identity-input>

                    {{-- Scan panel --}}
                    <div data-panel="scan">
                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-950">
                            <div class="relative aspect-[4/5]">
                                <video class="h-full w-full object-cover" autoplay playsinline muted data-scanner-video></video>
                                <div class="pointer-events-none absolute inset-0 flex items-center justify-center p-6">
                                    <div class="relative h-full w-full max-h-[280px] max-w-[280px] rounded-[2rem] border-2 border-white/60 shadow-[0_0_0_9999px_rgba(2,6,23,.5)]">
                                        <span class="absolute -left-0.5 -top-0.5 h-9 w-9 rounded-tl-2xl border-l-[3px] border-t-[3px] border-emerald-400"></span>
                                        <span class="absolute -right-0.5 -top-0.5 h-9 w-9 rounded-tr-2xl border-r-[3px] border-t-[3px] border-emerald-400"></span>
                                        <span class="absolute -bottom-0.5 -left-0.5 h-9 w-9 rounded-bl-2xl border-b-[3px] border-l-[3px] border-emerald-400"></span>
                                        <span class="absolute -bottom-0.5 -right-0.5 h-9 w-9 rounded-br-2xl border-b-[3px] border-r-[3px] border-emerald-400"></span>
                                        <span class="absolute left-4 right-4 top-1/2 h-px -translate-y-1/2 bg-emerald-400/60 shadow-[0_0_12px_rgba(52,211,153,.6)]"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="mt-3 flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                            <div class="min-w-0">
                                <p class="text-xs text-slate-400 font-medium">Status</p>
                                <p class="text-sm text-slate-800 font-medium mt-0.5 truncate" data-scan-status>Mempersiapkan kamera...</p>
                            </div>
                            <button type="button"
                                class="shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-slate-300 hover:text-slate-900 transition-colors duration-150"
                                data-restart-scan>
                                Ulangi
                            </button>
                        </div>

                        {{-- Result --}}
                        <div class="mt-2 rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-xs text-slate-400 font-medium">Hasil scan</p>
                            <p class="mt-0.5 font-mono text-sm text-slate-700 break-all" data-scan-result>{{ old('identity_number', '—') }}</p>
                        </div>

                        @error('identity_number')
                            <p class="text-xs text-red-500 flex items-center gap-1 mt-2">
                                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror

                        <p class="mt-3 text-xs text-slate-400 text-center leading-relaxed">
                            Izinkan akses kamera saat diminta. Setelah QR terbaca, halaman akan lanjut otomatis.
                        </p>
                    </div>{{-- end scan panel --}}

                    {{-- Manual panel --}}
                    <div data-panel="manual" class="hidden space-y-4">
                        <div>
                            <label for="identity_number_manual" class="block text-sm font-medium text-slate-700 mb-1.5">Nomor Identitas</label>
                            <input
                                id="identity_number_manual"
                                type="text"
                                name="identity_number_manual"
                                autocomplete="off"
                                placeholder="Contoh: LPG-JKT-001"
                                value="{{ old('identity_number') }}"
                                class="w-full px-4 py-2.5 text-sm font-mono text-slate-900 bg-slate-50 border border-slate-200 rounded-xl outline-none transition-all duration-200
                                       placeholder:text-slate-300 placeholder:font-sans
                                       focus:border-slate-400 focus:bg-white focus:ring-3 focus:ring-slate-100"
                                data-manual-input
                            >
                            @error('identity_number')
                                <p class="text-xs text-red-500 flex items-center gap-1 mt-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <button type="submit"
                            class="w-full bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white text-sm font-medium py-2.5 px-4 rounded-xl transition-all duration-150 cursor-pointer">
                            Verifikasi Identitas
                        </button>
                    </div>
                </form>

                {{-- Login link --}}
                <p class="mt-6 text-sm text-slate-400 text-center">
                    Punya akun admin?
                    <a href="{{ route('login') }}" class="text-slate-700 font-medium hover:text-slate-900 transition-colors duration-150">
                        Masuk di sini
                    </a>
                </p>

            </div>
        </div>
    </div>

    <style>
        .tab-active { background: white; color: #0f172a; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .tab-btn:not(.tab-active) { color: #94a3b8; }
    </style>

    <script>
        (() => {
            const tabBtns = document.querySelectorAll('.tab-btn');
            const panels = document.querySelectorAll('[data-panel]');
            const manualTextInput = document.querySelector('[data-manual-input]');
            const identityHidden = document.querySelector('[data-identity-input]');

            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const target = btn.dataset.tab;
                    tabBtns.forEach(b => { b.classList.remove('tab-active'); b.setAttribute('aria-selected', 'false'); });
                    btn.classList.add('tab-active');
                    btn.setAttribute('aria-selected', 'true');
                    panels.forEach(p => p.classList.toggle('hidden', p.dataset.panel !== target));
                    if (target === 'scan') {
                        startScanner();
                    } else {
                        stopScanner();
                        if (manualTextInput) manualTextInput.focus();
                    }
                });
            });

            const form = document.querySelector('[data-scan-form]');
            if (form && manualTextInput && identityHidden) {
                form.addEventListener('submit', () => {
                    const activePanel = document.querySelector('[data-panel]:not(.hidden)');
                    if (activePanel && activePanel.dataset.panel === 'manual') {
                        identityHidden.value = manualTextInput.value.trim();
                    }
                });
            }

            const video = document.querySelector('[data-scanner-video]');
            const statusLabel = document.querySelector('[data-scan-status]');
            const resultLabel = document.querySelector('[data-scan-result]');
            const restartButton = document.querySelector('[data-restart-scan]');

            if (!form || !video || !identityHidden || !statusLabel || !resultLabel || !restartButton) return;

            const hasDetector = 'BarcodeDetector' in window;
            let detector = null, stream = null, scanning = false, rafId = null, submitting = false;

            const setStatus = (msg) => { statusLabel.textContent = msg; };
            const setResult = (msg) => { resultLabel.textContent = msg; };

            const stopScanner = () => {
                scanning = false;
                if (rafId) { cancelAnimationFrame(rafId); rafId = null; }
                if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
                video.srcObject = null;
            };

            const submitIdentity = (value) => {
                if (submitting) return;
                submitting = true;
                identityHidden.value = value;
                setResult(value);
                setStatus('QR code terbaca. Membuka form lapangan...');
                stopScanner();
                form.submit();
            };

            const scanFrame = async () => {
                if (!scanning || !detector || video.readyState < 2) {
                    rafId = requestAnimationFrame(scanFrame);
                    return;
                }
                try {
                    const codes = await detector.detect(video);
                    if (codes.length > 0) {
                        const rawValue = (codes[0].rawValue || '').trim();
                        if (rawValue) { submitIdentity(rawValue); return; }
                    }
                } catch {
                    setStatus('Pembacaan QR code bermasalah. Coba arahkan ulang.');
                }
                rafId = requestAnimationFrame(scanFrame);
            };

            const startScanner = async () => {
                stopScanner();
                submitting = false;

                if (!hasDetector) {
                    setStatus('Browser ini belum mendukung scan QR. Gunakan Chrome atau Edge mobile.');
                    setResult('Scan tidak tersedia di browser ini');
                    return;
                }

                try {
                    const formats = await window.BarcodeDetector.getSupportedFormats();
                    if (!formats.includes('qr_code')) {
                        setStatus('Perangkat ini belum mendukung format QR code.');
                        setResult('Scan QR tidak tersedia di perangkat ini');
                        return;
                    }
                } catch {
                    setStatus('Dukungan format scanner tidak bisa dipastikan.');
                    setResult('Gagal memeriksa dukungan QR code');
                    return;
                }

                detector = new window.BarcodeDetector({ formats: ['qr_code'] });

                try {
                    setStatus('Meminta izin kamera...');
                    stream = await navigator.mediaDevices.getUserMedia({ audio: false, video: { facingMode: { ideal: 'environment' } } });
                    video.srcObject = stream;
                    await video.play();
                    scanning = true;
                    setStatus('Arahkan QR code ke kotak scan.');
                    setResult('Menunggu hasil scan...');
                    rafId = requestAnimationFrame(scanFrame);
                } catch {
                    setStatus('Akses kamera ditolak atau tidak tersedia.');
                    setResult('Tidak bisa memulai kamera');
                }
            };

            restartButton.addEventListener('click', startScanner);
            window.addEventListener('beforeunload', stopScanner);

            @if ($errors->has('identity_number') && old('identity_number'))
                const manualBtn = document.querySelector('[data-tab="manual"]');
                if (manualBtn) manualBtn.click();
            @else
                startScanner();
            @endif
        })();
    </script>
</x-layouts.app>
