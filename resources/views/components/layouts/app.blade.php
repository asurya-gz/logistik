<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Sistem Logistik' }}</title>
    <style>
        :root {
            --bg: #f3efe7;
            --panel: #fffdf9;
            --text: #1f2937;
            --muted: #6b7280;
            --line: #d6d3d1;
            --brand: #0f766e;
            --brand-soft: #ccfbf1;
            --danger: #b91c1c;
            --danger-soft: #fee2e2;
            --warn: #a16207;
            --warn-soft: #fef3c7;
            --ok: #166534;
            --ok-soft: #dcfce7;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Georgia, "Times New Roman", serif; color: var(--text); background:
            radial-gradient(circle at top left, rgba(15,118,110,.15), transparent 28%),
            linear-gradient(180deg, #f8f5ef 0%, var(--bg) 100%); }
        a { color: inherit; text-decoration: none; }
        .shell { min-height: 100vh; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(31,41,55,.08); background: rgba(255,253,249,.82); backdrop-filter: blur(10px); position: sticky; top: 0; z-index: 10; }
        .brand h1 { margin: 0; font-size: 1.2rem; }
        .brand p { margin: .2rem 0 0; color: var(--muted); font-size: .92rem; }
        .topbar nav { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }
        .nav-link, .button { border: 1px solid var(--line); background: var(--panel); border-radius: 999px; padding: .68rem 1rem; font-size: .92rem; cursor: pointer; }
        .nav-link.active { background: #111827; color: white; border-color: #111827; }
        .button-primary { background: var(--brand); color: white; border-color: var(--brand); }
        .container { max-width: 1200px; margin: 0 auto; padding: 1.5rem; }
        .grid { display: grid; gap: 1rem; }
        .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .card { background: rgba(255,253,249,.9); border: 1px solid rgba(31,41,55,.08); border-radius: 22px; padding: 1.2rem; box-shadow: 0 15px 35px rgba(15,23,42,.05); }
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
        form.inline { display: inline; }
        .toolbar, .form-grid { display: grid; gap: 1rem; }
        .toolbar { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); align-items: end; }
        label { display: block; font-size: .9rem; margin-bottom: .35rem; }
        input, select, textarea { width: 100%; border: 1px solid var(--line); border-radius: 14px; padding: .8rem .9rem; background: white; font: inherit; }
        textarea { min-height: 120px; resize: vertical; }
        .actions { display: flex; gap: .6rem; flex-wrap: wrap; }
        .empty { padding: 2rem; text-align: center; color: var(--muted); border: 1px dashed var(--line); border-radius: 16px; }
        .chart-bar { height: 14px; background: #e5e7eb; border-radius: 999px; overflow: hidden; }
        .chart-bar span { display: block; height: 100%; background: linear-gradient(90deg, #0f766e, #14b8a6); }
        .activity-item { padding: .8rem 0; border-bottom: 1px solid rgba(31,41,55,.08); }
        .activity-item:last-child { border-bottom: 0; }
        .auth-wrap { min-height: 100vh; display: grid; place-items: center; padding: 2rem; }
        .auth-card { width: min(460px, 100%); }
        .help-box { background: #111827; color: #f9fafb; border-radius: 22px; padding: 1.2rem; }
        .help-box code { color: #99f6e4; }
        .error-text { color: var(--danger); font-size: .85rem; margin-top: .35rem; }
        .pagination { display: flex; gap: .5rem; justify-content: flex-end; padding-top: 1rem; }
        @media (max-width: 900px) {
            .hero, .grid-4, .grid-3, .grid-2, .stats { grid-template-columns: 1fr; }
            .topbar { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
    @if (auth()->check())
        <div class="shell">
            <header class="topbar">
                <div class="brand">
                    <h1>Sistem Manajemen Logistik</h1>
                    <p>{{ auth()->user()->name }} - {{ str_replace('_', ' ', auth()->user()->role) }}</p>
                </div>
                <nav>
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="nav-link {{ request()->routeIs('logistics.*') ? 'active' : '' }}" href="{{ route('logistics.index') }}">Logistik</a>
                    <a class="nav-link {{ request()->routeIs('uploads.*') ? 'active' : '' }}" href="{{ route('uploads.index') }}">Upload</a>
                    @if (auth()->user()->canVerify())
                        <a class="nav-link {{ request()->routeIs('verifications.*') ? 'active' : '' }}" href="{{ route('verifications.index') }}">Verifikasi</a>
                    @endif
                    @if (auth()->user()->isSuperAdmin())
                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">User</a>
                        <a class="nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}" href="{{ route('branches.index') }}">Cabang</a>
                    @endif
                    <form class="inline" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="button" type="submit">Logout</button>
                    </form>
                </nav>
            </header>
            <main class="container">
                @if (session('success'))
                    <div class="flash flash-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="flash flash-error">{{ session('error') }}</div>
                @endif
                {{ $slot }}
            </main>
        </div>
    @else
        {{ $slot }}
    @endif
</body>
</html>
