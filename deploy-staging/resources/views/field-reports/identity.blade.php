<x-layouts.app :title="'Akses Lapangan'">
    <div class="min-h-screen bg-slate-50 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

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

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-slate-900">Akses Form Lapangan</h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Masukkan nomor identitas pekerja lapangan yang telah didaftarkan oleh manajemen kantor sebelum membuka form.
                    </p>
                </div>

                <form method="POST" action="{{ route('field-reports.verify') }}" class="space-y-5">
                    @csrf

                    <div class="space-y-1.5">
                        <label for="identity_number" class="block text-sm font-medium text-slate-700">
                            Nomor Identitas Pekerja Lapangan
                        </label>
                        <input
                            id="identity_number"
                            name="identity_number"
                            type="text"
                            value="{{ old('identity_number') }}"
                            placeholder="Contoh: LPG-JKT-001"
                            required
                            autofocus
                            class="w-full px-3.5 py-2.5 text-sm text-slate-900 bg-white border rounded-lg outline-none transition
                                   placeholder:text-slate-400
                                   {{ $errors->has('identity_number') ? 'border-red-400 focus:ring-2 focus:ring-red-100' : 'border-slate-300 focus:border-slate-400 focus:ring-2 focus:ring-slate-100' }}"
                        >
                        @error('identity_number')
                            <p class="text-xs text-red-500 flex items-center gap-1 mt-1">
                                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white text-sm font-medium py-2.5 px-4 rounded-lg transition-colors duration-150 cursor-pointer"
                    >
                        Lanjut ke Form
                    </button>
                </form>

                <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                    Verifikasi ini memastikan penginput terdeteksi dan hanya pekerja yang diizinkan yang dapat mengisi laporan.
                </div>
            </div>

            <div class="mt-4 bg-white rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Demo Akses</p>
                <div class="flex flex-col gap-2 text-xs sm:flex-row sm:items-center sm:justify-between">
                    <span class="text-slate-500 w-28 shrink-0">Identitas</span>
                    <code class="text-slate-700 bg-slate-50 border border-slate-200 rounded px-2 py-0.5 font-mono truncate">
                        LPG-JKT-001
                    </code>
                </div>
                <div class="mt-4 border-t border-slate-200 pt-3 text-xs text-slate-500">
                    Halaman ini sengaja dibuat konsisten dengan tampilan login agar pengalaman user tetap seragam.
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
