<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') • ProposalCRM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind = {
            config: {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
                            display: ['Sora', 'Inter', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
                        },
                        colors: {
                            brand: {
                                50: '#f4f4f5',
                                100: '#e4e4e7',
                                200: '#d4d4d8',
                                500: '#52525b',
                                600: '#3f3f46',
                                700: '#27272a',
                                800: '#18181b',
                            },
                        },
                    },
                },
            },
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --app-bg: #fafafc;
            --app-bg-soft: #f4f4f5;
            --surface: rgba(255, 255, 255, 0.96);
            --surface-strong: #ffffff;
            --surface-muted: #fafafa;
            --border-soft: #e4e4e7;
            --border-strong: #d4d4d8;
            --text-strong: #09090b;
            --text-body: #27272a;
            --text-muted: #71717a;
            --brand-50: #f4f4f5;
            --brand-100: #e4e4e7;
            --brand-200: #d4d4d8;
            --brand-500: #52525b;
            --brand-600: #3f3f46;
            --brand-700: #27272a;
            --brand-800: #18181b;
            --sidebar: #09090b;
            --sidebar-soft: #111827;
            --shadow-soft: 0 1px 2px rgba(15, 23, 42, 0.04), 0 10px 24px -18px rgba(15, 23, 42, 0.22);
            --shadow-panel: 0 22px 52px -38px rgba(15, 23, 42, 0.28);
            --font-sans-stack: 'Inter', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            --font-display-stack: 'Sora', 'Inter', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        [x-cloak] { display: none !important; }

        body {
            background:
                radial-gradient(circle at top, rgba(15, 23, 42, 0.045), transparent 32%),
                linear-gradient(180deg, var(--app-bg) 0%, var(--app-bg-soft) 100%);
            color: var(--text-body);
            font-family: var(--font-sans-stack);
            letter-spacing: -0.01em;
            text-rendering: optimizeLegibility;
            font-feature-settings: 'cv02' 1, 'cv03' 1, 'cv04' 1, 'cv11' 1;
        }

        .rounded-sm { border-radius: 0.2rem !important; }
        .rounded,
        .rounded-md { border-radius: 0.3rem !important; }
        .rounded-lg { border-radius: 0.4rem !important; }
        .rounded-xl { border-radius: 0.5rem !important; }
        .rounded-2xl { border-radius: 0.6rem !important; }
        .rounded-3xl { border-radius: 0.7rem !important; }
        .rounded-full { border-radius: 0.55rem !important; }

        input, button, textarea, select {
            font-family: var(--font-sans-stack);
        }

        .font-display,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: var(--font-display-stack) !important;
        }

        .bg-white { background-color: var(--surface) !important; }
        .bg-gray-50 { background-color: var(--surface-muted) !important; }
        .bg-gray-100 { background-color: #f4f4f5 !important; }
        .border-gray-200 { border-color: var(--border-soft) !important; }
        .border-gray-300 { border-color: var(--border-strong) !important; }
        .text-gray-900 { color: var(--text-strong) !important; }
        .text-gray-800 { color: #18181b !important; }
        .text-gray-700 { color: var(--text-body) !important; }
        .text-gray-600 { color: #52525b !important; }
        .text-gray-500 { color: var(--text-muted) !important; }
        .text-gray-400 { color: #a1a1aa !important; }
        .shadow-sm { box-shadow: var(--shadow-soft) !important; }

        .bg-violet-50 { background-color: var(--brand-50) !important; }
        .bg-violet-100 { background-color: var(--brand-100) !important; }
        .bg-violet-600 { background-color: var(--brand-700) !important; }
        .bg-violet-700 { background-color: var(--brand-800) !important; }
        .bg-violet-500\/20 { background-color: rgba(39, 39, 42, 0.14) !important; }
        .text-violet-900 { color: var(--brand-800) !important; }
        .text-violet-800 { color: var(--brand-800) !important; }
        .text-violet-700 { color: var(--brand-700) !important; }
        .text-violet-600 { color: var(--brand-600) !important; }
        .text-violet-300 { color: #d4d4d8 !important; }
        .text-violet-200 { color: #e4e4e7 !important; }
        .border-violet-100 { border-color: rgba(39, 39, 42, 0.08) !important; }
        .border-violet-200 { border-color: rgba(39, 39, 42, 0.14) !important; }
        .ring-violet-300 { --tw-ring-color: rgba(63, 63, 70, 0.18) !important; }
        .focus\:border-violet-400:focus { border-color: var(--brand-600) !important; }
        .focus\:ring-violet-200:focus { --tw-ring-color: rgba(39, 39, 42, 0.1) !important; }
        .hover\:bg-violet-50:hover { background-color: var(--brand-50) !important; }
        .hover\:bg-violet-700:hover { background-color: var(--brand-800) !important; }
        .hover\:text-violet-700:hover { color: var(--brand-700) !important; }
        .hover\:text-violet-800:hover { color: var(--brand-800) !important; }
        .hover\:border-violet-200:hover { border-color: rgba(39, 39, 42, 0.18) !important; }
    </style>
    @stack('head')
</head>
<body class="flex h-screen overflow-hidden font-sans text-gray-700 antialiased selection:bg-brand-100 selection:text-brand-800">
    @php
        $primaryNav = [
            ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'ti ti-layout-dashboard'],
            ['route' => 'proposals.index', 'label' => 'Proposals', 'icon' => 'ti ti-file-description'],
            ['route' => 'jobs.index', 'label' => 'Jobs', 'icon' => 'ti ti-briefcase'],
            ['route' => 'employers.index', 'label' => 'Employers', 'icon' => 'ti ti-building'],
            ['route' => 'portfolio.index', 'label' => 'Portfolio', 'icon' => 'ti ti-folder'],
            ['route' => 'follow-ups.index', 'label' => 'Follow-ups', 'icon' => 'ti ti-clock'],
            ['route' => 'ai-tools.index', 'label' => 'AI Tools', 'icon' => 'ti ti-brain'],
        ];
        $settingsNav = ['route' => 'settings.index', 'label' => 'Settings', 'icon' => 'ti ti-settings'];
        $user = auth()->user();
    @endphp

    <aside class="flex w-64 shrink-0 flex-col border-r border-white/10 bg-[linear-gradient(180deg,var(--sidebar),var(--sidebar-soft))] text-slate-100 shadow-[18px_0_45px_-40px_rgba(15,23,42,0.6)]">
        <div class="border-b border-white/10 px-5 py-5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-lg text-slate-200 backdrop-blur-sm">
                    <i class="ti ti-robot"></i>
                </span>
                <div>
                    <div class="font-display text-lg font-semibold tracking-tight">Upwork CRM</div>
                    <div class="text-xs text-slate-400">Freelance intelligence, automated</div>
                </div>
            </a>
        </div>

        <nav class="flex-1 space-y-1.5 overflow-y-auto px-3.5 py-4">
            @foreach ($primaryNav as $item)
                @continue(! Route::has($item['route']))
                <a href="{{ route($item['route']) }}" class="flex items-center justify-between gap-3 rounded-xl px-3 py-2 text-[13px] font-medium transition {{ request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'border border-white/10 bg-white/10 text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.06)]' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <i class="{{ $item['icon'] }} text-base"></i>
                        <span>{{ $item['label'] }}</span>
                    </span>
                    @if ($item['route'] === 'follow-ups.index' && ! empty($todayFollowUpsCount))
                        <span class="min-w-5 rounded-full bg-white/10 px-1.5 py-0.5 text-center text-[11px] font-medium text-slate-200">{{ $todayFollowUpsCount }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="mt-auto space-y-3 border-t border-white/10 px-3.5 pb-3.5 pt-3.5">
            @if ($user?->isAdmin() && Route::has($settingsNav['route']))
                <a href="{{ route($settingsNav['route']) }}" class="flex items-center gap-3 rounded-xl px-3 py-2 text-[13px] font-medium transition {{ request()->routeIs($settingsNav['route']) ? 'border border-white/10 bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <i class="{{ $settingsNav['icon'] }} text-base"></i>
                    <span>{{ $settingsNav['label'] }}</span>
                </a>
            @endif

            @if ($user)
                <div class="rounded-xl border border-white/10 bg-white/5 p-3.5 shadow-[inset_0_1px_0_rgba(255,255,255,0.04)]">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-white">{{ $user->name }}</div>
                            <div class="text-xs text-slate-400">{{ $user->email }}</div>
                        </div>
                        <span class="rounded-full px-2 py-1 text-[11px] font-medium {{ $user->isAdmin() ? 'bg-white/10 text-slate-100' : 'bg-white/10 text-slate-200' }}">
                            {{ ucfirst($user->role->value) }}
                        </span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-white/10 bg-black/20 px-3 py-2 text-sm font-medium text-slate-100 transition hover:bg-black/30">
                            <i class="ti ti-logout-2"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="border-b border-gray-200 bg-white/88 px-5 py-3.5 backdrop-blur-xl">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="font-display text-2xl font-semibold tracking-[-0.03em] text-gray-900">@yield('title')</h1>
                    @hasSection('subtitle')
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">@yield('subtitle')</p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] text-gray-600 shadow-sm">
                        {{ $connectsRemaining ?? 0 }} connects left
                    </span>
                    @if (Route::has('proposals.create'))
                        <a href="{{ route('proposals.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-violet-600 px-3.5 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-violet-700">
                            <i class="ti ti-plus text-base"></i>
                            <span>Quick-add proposal</span>
                        </a>
                    @endif
                </div>
            </div>
        </header>

        <div class="space-y-3 px-5 pt-4">
            @foreach (['success' => 'emerald', 'error' => 'red', 'status' => 'blue'] as $flashKey => $flashColor)
                @if (session($flashKey))
                    <div x-data="{show:true}" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition class="rounded-xl border px-4 py-3 text-sm {{ $flashColor === 'red' ? 'border-red-200 bg-red-50 text-red-700' : 'border-gray-200 bg-white text-gray-700' }}">
                        {{ session($flashKey) }}
                    </div>
                @endif
            @endforeach
        </div>

        <main class="flex-1 overflow-y-auto p-5">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
