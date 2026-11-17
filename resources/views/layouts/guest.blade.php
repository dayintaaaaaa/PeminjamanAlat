<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Sistem Peminjaman Alat</title>
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-['Inter'] antialiased">
        <div class="min-h-screen flex">
            <div class="hidden md:flex md:w-2/5 bg-[var(--c-primary)] text-white flex-col items-center justify-center relative px-10 text-center">
                <h1 class="text-xl font-bold flex items-center gap-2">
                    <i class="bi bi-wrench-adjustable"></i> Sistem Peminjaman Alat
                </h1>
                <p class="mt-3 text-sm max-w-xs">Manage and borrow practical equipment easily, quickly, and accurately.</p>
                <p class="absolute bottom-6 text-xs font-semibold">© 2026 Peminjaman Alat. All Rights Reserved.</p>
            </div>

            <div class="flex-1 flex items-center justify-center bg-[#F7F9F9] p-6">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
