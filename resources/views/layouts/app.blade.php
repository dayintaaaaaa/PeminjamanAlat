<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Sistem Peminjaman Alat</title>
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <!--bootstrap-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-['Inter'] bg-[#F5F7F7] text-gray-800">
        <div class="flex min-h-screen">
            <x-sidebar />

            <div class="flex-1 flex flex-col min-w-0">
                <header class="bg-white shadow px-8 py-4 text-right text-sm text-gray-600">
                    Menu / <span class="font-semibold text-sm text-gray-900">{{ $header ?? '' }}</span>
                </header>

                <main class="p-8">{{ $slot }}</main>
            </div>
            <x-logout-modal />
        </div>
    </body>
</html>
