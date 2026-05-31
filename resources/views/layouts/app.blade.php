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
                                50: '#eef4ff',
                                100: '#dce7fb',
                                200: '#c4d4f5',
                                500: '#5877bf',
                                600: '#3f61a8',
                                700: '#314d84',
                                800: '#22365e',
                            },
                            sand: {
                                50: '#faf5ee',
                                100: '#f2e7d8',
                                200: '#e6d2b8',
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
            --app-bg-warm: #faf5ee;
            --app-bg-cool: #eef3f9;
            --surface: rgba(255, 255, 255, 0.92);
            --surface-strong: #ffffff;
            --surface-muted: #f5f7fb;
            --border-soft: #e4e9f0;
            --border-strong: #d1d8e1;
            --text-strong: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --brand-50: #eef4ff;
            --brand-100: #dce7fb;
            --brand-200: #c4d4f5;
            --brand-500: #5877bf;
            --brand-600: #3f61a8;
            --brand-700: #314d84;
            --brand-800: #22365e;
            --sidebar: #0f172a;
            --sidebar-soft: #172237;
            --warm-50: #faf5ee;
            --warm-100: #f2e7d8;
            --shadow-soft: 0 1px 2px rgba(15, 23, 42, 0.04), 0 18px 38px -24px rgba(15, 23, 42, 0.2);
            --shadow-panel: 0 24px 70px -42px rgba(15, 23, 42, 0.35);
            --font-sans-stack: 'Inter', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            --font-display-stack: 'Sora', 'Inter', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        [x-cloak] { display: none !important; }

        body {
            background:
                radial-gradient(circle at top left, rgba(242, 231, 216, 0.68), transparent 28%),
                radial-gradient(circle at top right, rgba(196, 212, 245, 0.42), transparent 34%),
                linear-gradient(180deg, var(--app-bg-warm) 0%, #f8f8fb 42%, var(--app-bg-cool) 100%);
            color: var(--text-body);
            font-family: var(--font-sans-stack);
            letter-spacing: -0.01em;
            text-rendering: optimizeLegibility;
            font-feature-settings: 'cv02' 1, 'cv03' 1, 'cv04' 1, 'cv11' 1;
        }

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
        .bg-gray-100 { background-color: #edf2f7 !important; }
        .border-gray-200 { border-color: var(--border-soft) !important; }
        .border-gray-300 { border-color: var(--border-strong) !important; }
        .text-gray-900 { color: var(--text-strong) !important; }
        .text-gray-800 { color: #1e293b !important; }
        .text-gray-700 { color: var(--text-body) !important; }
        .text-gray-600 { color: #475569 !important; }
        .text-gray-500 { color: var(--text-muted) !important; }
        .text-gray-400 { color: #7b8798 !important; }
        .shadow-sm { box-shadow: var(--shadow-soft) !important; }

        .bg-violet-50 { background-color: var(--brand-50) !important; }
        .bg-violet-600 { background-color: var(--brand-600) !important; }
        .bg-violet-700 { background-color: var(--brand-700) !important; }
        .bg-violet-500\/20 { background-color: rgba(63, 97, 168, 0.16) !important; }
        .text-violet-800 { color: var(--brand-800) !important; }
        .text-violet-700 { color: var(--brand-700) !important; }
        .text-violet-600 { color: var(--brand-600) !important; }
        .text-violet-300 { color: #cdd8f4 !important; }
        .text-violet-200 { color: #dce7fb !important; }
        .border-violet-100 { border-color: rgba(63, 97, 168, 0.12) !important; }
        .border-violet-200 { border-color: rgba(63, 97, 168, 0.18) !important; }
        .ring-violet-300 { --tw-ring-color: rgba(88, 119, 191, 0.32) !important; }
        .focus\:border-violet-400:focus { border-color: var(--brand-500) !important; }
        .focus\:ring-violet-200:focus { --tw-ring-color: rgba(196, 212, 245, 0.72) !important; }
        .hover\:bg-violet-50:hover { background-color: var(--brand-50) !important; }
        .hover\:bg-violet-700:hover { background-color: var(--brand-700) !important; }
        .hover\:text-violet-700:hover { color: var(--brand-700) !important; }
        .hover\:text-violet-800:hover { color: var(--brand-800) !important; }
        .hover\:border-violet-200:hover { border-color: rgba(63, 97, 168, 0.22) !important; }
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

    <aside class="w-72 shrink-0 flex flex-col border-r border-white/10 bg-[linear-gradient(180deg,var(--sidebar),var(--sidebar-soft))] text-slate-100 shadow-[18px_0_45px_-40px_rgba(15,23,42,0.6)]">
        <div class="border-b border-white/10 px-6 py-6">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-xl text-violet-200 backdrop-blur-sm">
                    <i class="ti ti-robot"></i>
                </span>
                <div>
                    <div class="font-display text-xl font-semibold tracking-tight">ProposalCRM</div>
                    <div class="text-xs text-slate-400">Freelance intelligence, automated</div>
                </div>
            </a>
        </div>

        <nav class="px-4 py-5 space-y-1 flex-1 overflow-y-auto">
            @foreach ($primaryNav as $item)
                @continue(! Route::has($item['route']))
                <a href="{{ route($item['route']) }}" class="flex items-center justify-between gap-3 rounded-2xl px-3 py-2.5 text-sm transition {{ request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'border border-white/10 bg-white/10 text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.06)]' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <i class="{{ $item['icon'] }} text-lg"></i>
                        <span>{{ $item['label'] }}</span>
                    </span>
                    @if ($item['route'] === 'follow-ups.index' && ! empty($todayFollowUpsCount))
                        <span class="min-w-6 rounded-full bg-violet-500/20 px-2 py-0.5 text-center text-xs font-semibold text-violet-200">{{ $todayFollowUpsCount }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="mt-auto space-y-4 border-t border-white/10 px-4 pb-4 pt-4">
            @if ($user?->isAdmin() && Route::has($settingsNav['route']))
                <a href="{{ route($settingsNav['route']) }}" class="flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm transition {{ request()->routeIs($settingsNav['route']) ? 'border border-white/10 bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <i class="{{ $settingsNav['icon'] }} text-lg"></i>
                    <span>{{ $settingsNav['label'] }}</span>
                </a>
            @endif

            @if ($user)
                <div class="rounded-[1.35rem] border border-white/10 bg-white/5 p-4 shadow-[inset_0_1px_0_rgba(255,255,255,0.04)]">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-white">{{ $user->name }}</div>
                            <div class="text-xs text-slate-400">{{ $user->email }}</div>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->isAdmin() ? 'bg-violet-500/20 text-violet-200' : 'bg-white/10 text-slate-200' }}">
                            {{ ucfirst($user->role->value) }}
                        </span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/10 bg-black/20 px-3 py-2 text-sm font-medium text-slate-100 transition hover:bg-black/30">
                            <i class="ti ti-logout-2"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="border-b border-gray-200 bg-white/70 px-6 py-4 backdrop-blur-xl">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="font-display text-[1.95rem] font-semibold tracking-[-0.03em] text-gray-900">@yield('title')</h1>
                    @hasSection('subtitle')
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">@yield('subtitle')</p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center rounded-full border border-gray-200 bg-white/80 px-3 py-1.5 text-sm font-semibold text-gray-700 shadow-sm">
                        {{ $connectsRemaining ?? 0 }} connects left
                    </span>
                    @if (Route::has('proposals.create'))
                        <a href="{{ route('proposals.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700">
                            <i class="ti ti-plus text-base"></i>
                            <span>Quick-add proposal</span>
                        </a>
                    @endif
                </div>
            </div>
        </header>

        <div class="px-6 pt-4 space-y-3">
            @foreach (['success' => 'emerald', 'error' => 'red', 'status' => 'blue'] as $flashKey => $flashColor)
                @if (session($flashKey))
                    <div x-data="{show:true}" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition class="rounded-2xl border px-4 py-3 text-sm {{ $flashColor === 'emerald' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : ($flashColor === 'red' ? 'border-red-200 bg-red-50 text-red-800' : 'border-blue-200 bg-blue-50 text-blue-800') }}">
                        {{ session($flashKey) }}
                    </div>
                @endif
            @endforeach
        </div>

        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
