<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ProposalCRM</title>
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
    <style>
        :root {
            --guest-bg: #fafafc;
            --guest-bg-soft: #f4f4f5;
            --guest-text: #27272a;
            --border-soft: #e4e4e7;
            --border-strong: #d4d4d8;
            --brand-50: #f4f4f5;
            --brand-100: #e4e4e7;
            --brand-600: #3f3f46;
            --brand-700: #27272a;
            --brand-800: #18181b;
            --font-sans-stack: 'Inter', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            --font-display-stack: 'Sora', 'Inter', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background:
                radial-gradient(circle at top, rgba(15, 23, 42, 0.05), transparent 34%),
                linear-gradient(180deg, var(--guest-bg) 0%, var(--guest-bg-soft) 100%);
            color: var(--guest-text);
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

        .bg-violet-100 { background-color: var(--brand-100) !important; }
        .bg-violet-600 { background-color: var(--brand-700) !important; }
        .bg-violet-700 { background-color: var(--brand-800) !important; }
        .text-violet-700 { color: var(--brand-700) !important; }
        .text-violet-600 { color: var(--brand-600) !important; }
        .border-violet-200 { border-color: rgba(39, 39, 42, 0.14) !important; }
        .focus\:border-violet-400:focus { border-color: var(--brand-600) !important; }
        .focus\:ring-violet-200:focus { --tw-ring-color: rgba(39, 39, 42, 0.1) !important; }
        .hover\:bg-violet-700:hover { background-color: var(--brand-800) !important; }
    </style>
</head>
<body class="min-h-screen font-sans text-gray-700 antialiased">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10">
        <div class="pointer-events-none absolute inset-x-0 top-[-14%] h-72 bg-[radial-gradient(circle,_rgba(15,23,42,0.06),_transparent_60%)]"></div>
        <div class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white p-7 shadow-[0_22px_52px_-38px_rgba(15,23,42,0.24)]">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
