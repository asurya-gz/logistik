<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Sistem Logistik' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg: #eef3f7;
            --bg-deep: #dfe8ef;
            --panel: rgba(255, 255, 255, .9);
            --panel-strong: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --line: rgba(148, 163, 184, .26);
            --brand: #0f766e;
            --brand-strong: #115e59;
            --brand-soft: #ccfbf1;
            --accent: #1d4ed8;
            --danger: #b91c1c;
            --danger-soft: #fee2e2;
            --warn: #a16207;
            --warn-soft: #fef3c7;
            --ok: #166534;
            --ok-soft: #dcfce7;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Instrument Sans", "Segoe UI", sans-serif; color: var(--text); background:
            radial-gradient(circle at top left, rgba(29,78,216,.14), transparent 24%),
            radial-gradient(circle at bottom right, rgba(15,118,110,.18), transparent 24%),
            linear-gradient(180deg, #f8fbfd 0%, var(--bg) 52%, var(--bg-deep) 100%); }
        a { text-decoration: none; }
        .shell { min-height: 100vh; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(31,41,55,.08); background: rgba(255,255,255,.72); backdrop-filter: blur(14px); position: sticky; top: 0; z-index: 10; }
        .brand h1 { margin: 0; font-size: 1.2rem; }
        .brand p { margin: .2rem 0 0; color: var(--muted); font-size: .92rem; }
        .topbar nav { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }
        .nav-link, .button { border: 1px solid var(--line); background: var(--panel); border-radius: 999px; padding: .68rem 1rem; font-size: .92rem; cursor: pointer; box-shadow: 0 8px 22px rgba(15, 23, 42, .05); }
        .nav-link.active { background: #111827; color: white; border-color: #111827; }
        .button-primary { background: linear-gradient(135deg, var(--brand) 0%, var(--accent) 100%); color: white; border-color: transparent; }
        .container { max-width: 1200px; margin: 0 auto; padding: 1.5rem; }
        .grid { display: grid; gap: 1rem; }
        .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .card { background: rgba(255,255,255,.82); border: 1px solid rgba(255,255,255,.8); border-radius: 22px; padding: 1.2rem; box-shadow: 0 18px 40px rgba(15,23,42,.07); backdrop-filter: blur(16px); }
        .card h2, .card h3 { margin-top: 0; }
        .hero { display: grid; grid-template-columns: 1.2fr .8fr; gap: 1rem; margin-bottom: 1rem; }
        .headline { font-size: clamp(2rem, 3vw, 3.4rem); line-height: 1.02; margin: 0 0 .8rem; }
        .muted { color: var(--muted); }
        .flash { padding: .9rem 1rem; border-radius: 16px; margin-bottom: 1rem; border: 1px solid transparent; }
        .flash-success { background: var(--ok-soft); color: var(--ok); border-color: rgba(22,101,52,.2); }
        .flash-error { background: var(--danger-soft); color: var(--danger); border-color: rgba(185,28,28,.2); }
        .stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; margin: 1rem 0; }
        .stat-number { font-size: 2rem; font-weight: 700; margin: .4rem 0; }
        .badge { display: inline-flex; align-items: center; padding: .3rem .7rem; border-radius: 999px; font-size: .8rem; }
        .badge-pending { background: var(--warn-soft); color: var(--warn); }
        .badge-approved { background: var(--ok-soft); color: var(--ok); }
        .badge-rejected { background: var(--danger-soft); color: var(--danger); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: .85rem .75rem; border-bottom: 1px solid rgba(31,41,55,.08); text-align: left; vertical-align: top; }
        th { color: var(--muted); font-size: .86rem; text-transform: uppercase; letter-spacing: .04em; }
        .table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-wrap table { min-width: 720px; }
        form.inline { display: inline; }
        .toolbar, .form-grid { display: grid; gap: 1rem; }
        .toolbar { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); align-items: end; }
        label { display: block; font-size: .9rem; margin-bottom: .45rem; color: #334155; font-weight: 600; }
        input, select, textarea { width: 100%; border: 1px solid var(--line); border-radius: 16px; padding: .9rem 1rem; background: rgba(255,255,255,.94); font: inherit; transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: rgba(29,78,216,.45); box-shadow: 0 0 0 4px rgba(29,78,216,.12); transform: translateY(-1px); }
        textarea { min-height: 120px; resize: vertical; }
        .actions { display: flex; gap: .6rem; flex-wrap: wrap; }
        .empty { padding: 2rem; text-align: center; color: var(--muted); border: 1px dashed var(--line); border-radius: 16px; }
        .chart-bar { height: 14px; background: #e5e7eb; border-radius: 999px; overflow: hidden; }
        .chart-bar span { display: block; height: 100%; background: linear-gradient(90deg, #0f766e, #14b8a6); }
        .activity-item { padding: .8rem 0; border-bottom: 1px solid rgba(31,41,55,.08); }
        .activity-item:last-child { border-bottom: 0; }
        .auth-wrap { min-height: 100vh; display: grid; place-items: center; padding: 2rem; position: relative; overflow: hidden; }
        .auth-wrap::before,
        .auth-wrap::after { content: ""; position: absolute; border-radius: 999px; filter: blur(8px); opacity: .75; }
        .auth-wrap::before { width: 22rem; height: 22rem; background: radial-gradient(circle, rgba(29,78,216,.22) 0%, rgba(29,78,216,0) 68%); top: 4rem; left: -4rem; }
        .auth-wrap::after { width: 26rem; height: 26rem; background: radial-gradient(circle, rgba(15,118,110,.2) 0%, rgba(15,118,110,0) 70%); right: -8rem; bottom: -4rem; }
        .auth-shell { width: min(1100px, 100%); display: grid; grid-template-columns: 1.05fr .95fr; gap: 1.25rem; position: relative; z-index: 1; }
        .auth-panel { padding: 2rem; border-radius: 30px; border: 1px solid rgba(255,255,255,.7); background: linear-gradient(180deg, rgba(10,37,64,.96) 0%, rgba(15,23,42,.92) 100%); color: #e2e8f0; box-shadow: 0 30px 70px rgba(15,23,42,.18); }
        .auth-panel .eyebrow { display: inline-flex; align-items: center; gap: .5rem; padding: .5rem .8rem; border-radius: 999px; background: rgba(255,255,255,.08); color: #cbd5e1; font-size: .82rem; letter-spacing: .04em; text-transform: uppercase; }
        .auth-panel h1 { margin: 1.2rem 0 .9rem; font-size: clamp(2.4rem, 4vw, 4rem); line-height: 1; letter-spacing: -.04em; color: #f8fafc; }
        .auth-panel p { color: #cbd5e1; font-size: 1rem; line-height: 1.7; max-width: 34rem; }
        .auth-features { display: grid; gap: .9rem; margin-top: 2rem; }
        .auth-feature { padding: 1rem 1.1rem; border-radius: 18px; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.08); }
        .auth-feature strong { display: block; color: #f8fafc; margin-bottom: .25rem; }
        .auth-card { width: 100%; padding: 2rem; border-radius: 30px; background: rgba(255,255,255,.8); border: 1px solid rgba(255,255,255,.8); box-shadow: 0 30px 70px rgba(15,23,42,.12); backdrop-filter: blur(18px); }
        .auth-card-header { margin-bottom: 1.6rem; }
        .auth-kicker { display: inline-block; font-size: .8rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--brand-strong); margin-bottom: .7rem; }
        .auth-title { margin: 0 0 .45rem; font-size: 2rem; line-height: 1.08; letter-spacing: -.03em; }
        .auth-subtitle { margin: 0; color: var(--muted); line-height: 1.6; }
        .auth-form { display: grid; gap: 1rem; }
        .auth-field { display: grid; gap: .4rem; }
        .auth-options { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-top: .15rem; }
        .auth-check { display: inline-flex; align-items: center; gap: .7rem; color: var(--muted); font-size: .94rem; }
        .auth-check input { width: 1rem; height: 1rem; padding: 0; border-radius: 999px; box-shadow: none; transform: none; accent-color: var(--brand); }
        .auth-submit { width: 100%; padding: 1rem 1.2rem; font-weight: 700; font-size: .98rem; }
        .help-box { margin-top: 1.4rem; background: linear-gradient(180deg, rgba(248,250,252,.95) 0%, rgba(241,245,249,.95) 100%); color: #0f172a; border: 1px solid rgba(148,163,184,.18); border-radius: 22px; padding: 1.2rem; }
        .help-box code { color: var(--brand-strong); font-weight: 700; }
        .auth-meta { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-top: 1.1rem; color: var(--muted); font-size: .9rem; }
        .error-text { color: var(--danger); font-size: .85rem; margin-top: .35rem; }
        .pagination { display: flex; gap: .5rem; justify-content: flex-end; padding-top: 1rem; }
        @media (max-width: 900px) {
            .hero, .grid-4, .grid-3, .grid-2, .stats { grid-template-columns: 1fr; }
            .topbar { align-items: flex-start; flex-direction: column; }
            .auth-shell { grid-template-columns: 1fr; }
            .auth-panel, .auth-card { padding: 1.5rem; }
            .auth-panel h1 { font-size: 2.5rem; }
            .auth-options, .auth-meta { flex-direction: column; align-items: flex-start; }
        }
        @media (max-width: 640px) {
            .card { padding: 1rem; border-radius: 18px; }
            th, td { padding: .75rem .65rem; }
            .toolbar { grid-template-columns: 1fr; }
            .pagination { justify-content: flex-start; overflow-x: auto; }
        }
        /* Mobile sidebar scroll */
        #sidebar { overflow-y: auto; -webkit-overflow-scrolling: touch; }
    </style>
</head>
<body>
    @if (auth()->check())
        @php
            $user = auth()->user();
            $navItems = [
                [
                    'label' => 'Dashboard',
                    'route' => route($user->dashboardRouteName()),
                    'active' => request()->routeIs('dashboard') || request()->routeIs('*.dashboard'),
                    'icon' => 'dashboard',
                ],
                [
                    'label' => 'Informasi',
                    'route' => route($user->panelRouteName('logistics.index')),
                    'active' => request()->routeIs('*.logistics.*'),
                    'icon' => 'logistics',
                ],
            ];

            if ($user->canManageItems()) {
                $navItems[] = [
                    'label' => 'Barang',
                    'route' => route('superadmin.items.index'),
                    'active' => request()->routeIs('superadmin.items.*'),
                    'icon' => 'items',
                ];
            }

            if ($user->canSuggestItems() || $user->canManageItems()) {
                $navItems[] = [
                    'label' => 'Usulan Barang',
                    'route' => route($user->panelRouteName('item-suggestions.index')),
                    'active' => request()->routeIs('*.item-suggestions.*'),
                    'icon' => 'suggestions',
                ];
            }

            if ($user->canViewVerifications()) {
                $navItems[] = [
                    'label' => 'Verifikasi',
                    'route' => route($user->panelRouteName('verifications.index')),
                    'active' => request()->routeIs('*.verifications.*'),
                    'icon' => 'verification',
                ];
            }

            if ($user->canManageUsers()) {
                $navItems[] = [
                    'label' => 'User',
                    'route' => route('superadmin.users.index'),
                    'active' => request()->routeIs('superadmin.users.*'),
                    'icon' => 'users',
                ];
            }

            if ($user->canManageBranches()) {
                $navItems[] = [
                    'label' => 'Cabang',
                    'route' => route('superadmin.branches.index'),
                    'active' => request()->routeIs('superadmin.branches.*'),
                    'icon' => 'branches',
                ];
            }
        @endphp
        @php
            if ($user->canManageItems()) {
                $navItems[] = ['label' => 'Finalisasi', 'route' => route('superadmin.finalisasi.index'), 'active' => request()->routeIs('superadmin.finalisasi.*'), 'icon' => 'finalisasi'];
            }
            $initials = collect(explode(' ', $user->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
            $roleLabel = \App\Models\User::roleOptions()[$user->role] ?? $user->role;
        @endphp
        <div class="min-h-screen lg:grid lg:h-screen lg:grid-cols-[220px_minmax(0,1fr)] lg:overflow-hidden">

            {{-- Mobile top bar --}}
            <div class="lg:hidden flex items-center justify-between px-4 py-3 bg-[#0e1117] text-white sticky top-0 z-30 shadow-lg">
                <button id="mobile-menu-btn" type="button" class="flex items-center justify-center h-9 w-9 rounded-lg hover:bg-white/10 transition-colors" aria-label="Buka menu">
                    <svg id="menu-icon-open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <div class="flex items-center gap-2">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-indigo-500/20 ring-1 ring-indigo-400/20">
                        <svg class="h-3.5 w-3.5 text-indigo-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/>
                            <path d="M16.5 9.4 7.55 4.24"/>
                            <polyline points="3.29 7 12 12 20.71 7"/>
                            <line x1="12" x2="12" y1="22" y2="12"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold">Sistem Logistik</span>
                </div>
                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-500/25 text-[10px] font-bold text-indigo-300 ring-1 ring-indigo-400/20">
                    {{ $initials }}
                </div>
            </div>

            {{-- Sidebar overlay backdrop (mobile only) --}}
            <div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-black/50 hidden lg:hidden transition-opacity duration-300"></div>

            {{-- Sidebar --}}
            <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-[260px] flex flex-col bg-[#0e1117] px-3 py-4 transform -translate-x-full transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 lg:w-auto lg:h-screen lg:z-auto" style="border-right:1px solid rgba(255,255,255,0.06);color:white">

                {{-- Mobile close button --}}
                <button id="sidebar-close-btn" type="button" class="lg:hidden absolute top-3 right-3 flex items-center justify-center h-8 w-8 rounded-lg hover:bg-white/10 transition-colors" aria-label="Tutup menu">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>

                {{-- Brand --}}
                <div class="flex items-center gap-2.5 px-2 mb-5">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-500/20 ring-1 ring-indigo-400/20">
                        <svg class="h-4 w-4 text-indigo-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/>
                            <path d="M16.5 9.4 7.55 4.24"/>
                            <polyline points="3.29 7 12 12 20.71 7"/>
                            <line x1="12" x2="12" y1="22" y2="12"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[13px] font-semibold text-white leading-tight truncate">Sistem Logistik</p>
                        <p class="text-[11px] text-slate-500 leading-tight">Multi cabang</p>
                    </div>
                </div>

                {{-- Nav section label --}}
                <p class="px-2.5 mb-1.5 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Menu</p>

                {{-- Nav --}}
                <nav class="flex-1 space-y-0.5">
                    @foreach ($navItems as $item)
                        <a href="{{ $item['route'] }}"
                            class="group flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-[13px] font-medium transition-all
                                {{ $item['active']
                                    ? 'bg-white/8 text-white'
                                    : 'text-white hover:bg-white/4' }}">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center
                                {{ $item['active'] ? 'text-indigo-300' : 'text-slate-400 group-hover:text-slate-200' }}">
                                @switch($item['icon'])
                                    @case('dashboard')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                                            <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                                            <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                                            <rect x="3" y="16" width="7" height="5" rx="1.5"/>
                                        </svg>
                                        @break
                                    @case('logistics')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 7.5 12 3l9 4.5-9 4.5L3 7.5Z"/>
                                            <path d="M3 12l9 4.5 9-4.5"/>
                                            <path d="M3 16.5 12 21l9-4.5"/>
                                        </svg>
                                        @break
                                    @case('items')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 16V8a2 2 0 0 0-1.1-1.79l-6-3a2 2 0 0 0-1.8 0l-6 3A2 2 0 0 0 5 8v8a2 2 0 0 0 1.1 1.79l6 3a2 2 0 0 0 1.8 0l6-3A2 2 0 0 0 21 16Z"/>
                                            <path d="m5.27 6.96 6.73 3.37 6.73-3.37"/>
                                            <path d="M12 10.33V21"/>
                                        </svg>
                                        @break
                                    @case('suggestions')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                        </svg>
                                        @break
                                    @case('verification')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9 12.75 11.25 15 15.5 9.75"/>
                                            <path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9Z"/>
                                        </svg>
                                        @break
                                    @case('finalisasi')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9 11l3 3L22 4"/>
                                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                        </svg>
                                        @break
                                    @case('users')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                        @break
                                    @case('branches')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 21h18"/>
                                            <path d="M5 21V7l7-4 7 4v14"/>
                                            <path d="M9 10h.01"/>
                                            <path d="M15 10h.01"/>
                                            <path d="M9 14h.01"/>
                                            <path d="M15 14h.01"/>
                                        </svg>
                                        @break
                                @endswitch
                            </span>
                            {{ $item['label'] }}
                            @if ($item['active'])
                                <span class="ml-auto h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                            @endif
                        </a>
                    @endforeach
                </nav>

                {{-- User footer --}}
                <div class="mt-4 pt-4" style="border-top: 1px solid rgba(255,255,255,0.07)">
                    <div class="mb-1 flex items-center gap-2.5 rounded-lg px-2.5 py-2">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-500/25 text-[11px] font-bold text-indigo-300 ring-1 ring-indigo-400/20">
                            {{ $initials }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-[12px] font-semibold text-slate-200 leading-tight truncate">{{ $user->name }}</p>
                            <p class="text-[11px] text-slate-500 leading-tight truncate">{{ $roleLabel }}</p>
                        </div>
                    </div>
                    <form id="logout-form" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button id="open-logout-modal" type="button"
                            class="flex w-full items-center gap-2 rounded-xl px-2.5 py-2 text-[13px] text-slate-500 transition hover:bg-white/5 hover:text-red-400">
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <path d="m16 17 5-5-5-5"/>
                                <path d="M21 12H9"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </aside>

            <main class="min-w-0 p-4 sm:p-5 lg:h-screen lg:overflow-y-auto">
                <div class="mx-auto max-w-7xl">
                @if (session('success'))
                    <div class="mb-4 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-4 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
                @endif
                    {{ $slot }}
                </div>
            </main>
        </div>

        <div id="logout-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/45 px-4">
            <div class="w-full max-w-sm rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_24px_80px_rgba(15,23,42,.18)]">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <path d="m16 17 5-5-5-5"/>
                        <path d="M21 12H9"/>
                    </svg>
                </div>
                <h2 class="mt-4 text-lg font-semibold text-slate-900">Konfirmasi logout</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Anda yakin ingin keluar dari sistem? Sesi login saat ini akan diakhiri.
                </p>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button
                        id="cancel-logout"
                        type="button"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900"
                    >
                        Batal
                    </button>
                    <button
                        id="confirm-logout"
                        type="button"
                        class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-100 hover:text-red-700"
                    >
                        Ya, logout
                    </button>
                </div>
            </div>
        </div>
    @else
        {{ $slot }}
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ── Mobile sidebar toggle ──
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const menuBtn = document.getElementById('mobile-menu-btn');
            const closeBtn = document.getElementById('sidebar-close-btn');

            const openSidebar = () => {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                backdrop.classList.remove('hidden');
                backdrop.classList.add('block');
                document.body.classList.add('overflow-hidden');
            };

            const closeSidebar = () => {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                backdrop.classList.add('hidden');
                backdrop.classList.remove('block');
                document.body.classList.remove('overflow-hidden');
            };

            if (menuBtn) menuBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);

            // Close sidebar when a nav link is clicked (mobile)
            if (sidebar) {
                sidebar.querySelectorAll('a[href]').forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth < 1024) closeSidebar();
                    });
                });
            }

            // ── Logout modal ──
            const openLogoutModalButton = document.getElementById('open-logout-modal');
            const logoutModal = document.getElementById('logout-modal');
            const cancelLogoutButton = document.getElementById('cancel-logout');
            const confirmLogoutButton = document.getElementById('confirm-logout');
            const logoutForm = document.getElementById('logout-form');

            if (!openLogoutModalButton || !logoutModal || !cancelLogoutButton || !confirmLogoutButton || !logoutForm) {
                return;
            }

            const openModal = () => {
                logoutModal.classList.remove('hidden');
                logoutModal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            };

            const closeModal = () => {
                logoutModal.classList.add('hidden');
                logoutModal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            };

            openLogoutModalButton.addEventListener('click', openModal);
            cancelLogoutButton.addEventListener('click', closeModal);
            confirmLogoutButton.addEventListener('click', () => logoutForm.submit());

            logoutModal.addEventListener('click', (event) => {
                if (event.target === logoutModal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    if (!logoutModal.classList.contains('hidden')) closeModal();
                    if (sidebar && !sidebar.classList.contains('-translate-x-full') && window.innerWidth < 1024) closeSidebar();
                }
            });
        });
    </script>
</body>
</html>
