<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ProposalCRM</title>
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
</head>
<body class="min-h-screen bg-gray-50 font-sans text-gray-700">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(124,58,237,0.10),_transparent_35%),linear-gradient(180deg,_rgba(249,250,251,1),_rgba(243,244,246,1))] flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md rounded-[28px] border border-gray-200 bg-white p-8 shadow-[0_20px_80px_-35px_rgba(15,23,42,0.45)]">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
