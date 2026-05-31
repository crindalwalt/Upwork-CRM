<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') • ProposalCRM</title>
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
    <style>
        :root {
            --auth-bg-warm: #faf5ee;
            --auth-bg-cool: #eef3f9;
            --auth-text: #334155;
            --font-sans-stack: 'Inter', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            --font-display-stack: 'Sora', 'Inter', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(242, 231, 216, 0.72), transparent 34%),
                radial-gradient(circle at top right, rgba(196, 212, 245, 0.48), transparent 36%),
                linear-gradient(180deg, var(--auth-bg-warm) 0%, #f8f8fb 44%, var(--auth-bg-cool) 100%);
            color: var(--auth-text);
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
    </style>
</head>
<body class="min-h-screen font-sans text-gray-700 antialiased">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10">
        <div class="pointer-events-none absolute inset-x-0 top-[-14%] h-72 bg-[radial-gradient(circle,_rgba(242,231,216,0.96),_transparent_60%)]"></div>
        <div class="pointer-events-none absolute right-[-10%] top-1/4 h-72 w-72 rounded-full bg-[rgba(220,231,251,0.82)] blur-3xl"></div>
        <div class="pointer-events-none absolute left-[-8%] bottom-[-4%] h-80 w-80 rounded-full bg-[rgba(15,23,42,0.07)] blur-3xl"></div>
        <div class="relative w-full max-w-md rounded-[2rem] border border-white/70 bg-white/80 p-8 shadow-[0_30px_100px_-52px_rgba(15,23,42,0.46)] backdrop-blur-xl">
            @yield('content')
        </div>
    </div>
</body>
</html>
