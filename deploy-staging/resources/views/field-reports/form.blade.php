<x-layouts.app :title="'Form Lapangan'">
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
                    <h2 class="text-lg font-semibold text-slate-900">Form Informasi Lapangan</h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Identitas aktif untuk <strong>{{ $fieldUser->name }}</strong> ({{ $fieldUser->identity_number }}).
                    </p>
                </div>

                <form method="POST" action="{{ route('field-reports.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700">Nama Pelapor</label>
                        <input
                            value="{{ $fieldUser->name }}"
                            disabled
                            class="w-full px-3.5 py-2.5 text-sm text-slate-500 bg-slate-50 border border-slate-200 rounded-lg outline-none"
                        >
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700">Cabang</label>
                        <input
                            value="{{ $fieldUser->branch?->name ?: '-' }}"
                            disabled
                            class="w-full px-3.5 py-2.5 text-sm text-slate-500 bg-slate-50 border border-slate-200 rounded-lg outline-none"
                        >
                    </div>

                    <div class="space-y-1.5">
                        <label for="photo" class="block text-sm font-medium text-slate-700">
                            Foto
                        </label>
                        <input
                            id="photo"
                            name="photo"
                            type="file"
                            accept="image/*"
                            required
                            class="w-full px-3.5 py-2.5 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg outline-none transition
                                   file:mr-3 file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:rounded-md file:text-slate-700
                                   focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                        >
                        @error('photo')
                            <p class="text-xs text-red-500 flex items-center gap-1 mt-1">
                                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="keterangan" class="block text-sm font-medium text-slate-700">
                            Keterangan
                        </label>
                        <textarea
                            id="keterangan"
                            name="keterangan"
                            required
                            class="w-full px-3.5 py-2.5 text-sm text-slate-900 bg-white border rounded-lg outline-none transition
                                   placeholder:text-slate-400
                                   {{ $errors->has('keterangan') ? 'border-red-400 focus:ring-2 focus:ring-red-100' : 'border-slate-300 focus:border-slate-400 focus:ring-2 focus:ring-slate-100' }}"
                        >{{ old('keterangan') }}</textarea>
                        @error('keterangan')
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
                        Kirim Informasi
                    </button>
                </form>

                <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                    Data ini akan tercatat atas nama identitas yang sedang aktif dan otomatis masuk ke cabang yang terdaftar.
                </div>
            </div>

            <div class="mt-4 bg-white rounded-xl border border-slate-200 p-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Identitas Aktif</p>
                        <p class="mt-1 text-sm text-slate-700">{{ $fieldUser->identity_number }}</p>
                    </div>
                    <form method="POST" action="{{ route('field-reports.logout') }}">
                        @csrf
                        <button class="button w-full sm:w-auto" type="submit">Ganti Identitas</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
