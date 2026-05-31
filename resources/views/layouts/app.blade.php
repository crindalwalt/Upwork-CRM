<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') • ProposalCRM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <script>
        tailwind = {
            config: {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui'],
                            display: ['Space Grotesk', 'ui-sans-serif', 'system-ui'],
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
        [x-cloak] { display: none !important; }
    </style>
    @stack('head')
</head>
<body class="bg-gray-50 font-sans text-gray-700 flex h-screen overflow-hidden">
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

    <aside class="bg-gray-900 text-gray-100 w-72 shrink-0 flex flex-col border-r border-gray-800">
        <div class="px-6 py-6 border-b border-gray-800">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="w-11 h-11 rounded-2xl bg-violet-600/20 text-violet-300 flex items-center justify-center text-xl">
                    <i class="ti ti-robot"></i>
                </span>
                <div>
                    <div class="font-display text-xl font-semibold tracking-tight">ProposalCRM</div>
                    <div class="text-xs text-gray-400">Freelance intelligence, automated</div>
                </div>
            </a>
        </div>

        <nav class="px-4 py-5 space-y-1 flex-1 overflow-y-auto">
            @foreach ($primaryNav as $item)
                @continue(! Route::has($item['route']))
                <a href="{{ route($item['route']) }}" class="flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm transition {{ request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
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

        <div class="px-4 pb-4 mt-auto space-y-4 border-t border-gray-800 pt-4">
            @if ($user?->isAdmin() && Route::has($settingsNav['route']))
                <a href="{{ route($settingsNav['route']) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition {{ request()->routeIs($settingsNav['route']) ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <i class="{{ $settingsNav['icon'] }} text-lg"></i>
                    <span>{{ $settingsNav['label'] }}</span>
                </a>
            @endif

            @if ($user)
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-white">{{ $user->name }}</div>
                            <div class="text-xs text-gray-400">{{ $user->email }}</div>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->isAdmin() ? 'bg-violet-500/20 text-violet-200' : 'bg-gray-800 text-gray-200' }}">
                            {{ ucfirst($user->role->value) }}
                        </span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/10 bg-gray-950/50 px-3 py-2 text-sm font-medium text-gray-100 transition hover:bg-gray-950">
                            <i class="ti ti-logout-2"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="border-b border-gray-200 bg-white/90 backdrop-blur-sm px-6 py-4">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 font-display">@yield('title')</h1>
                    @hasSection('subtitle')
                        <p class="mt-1 text-sm text-gray-400">@yield('subtitle')</p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700">
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
