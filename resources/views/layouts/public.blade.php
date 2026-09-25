<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SinauSubnet' }} — Belajar Subnetting Berbasis Masalah</title>
    <meta name="description" content="{{ $description ?? 'Platform pembelajaran subnetting jaringan komputer berbasis Problem-Based Learning untuk SMK.' }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-white font-sans text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100">

    {{-- ── Navbar Publik ──────────────────────────────────────────────────── --}}
    <header class="sticky top-0 z-50 border-b border-gray-200 bg-white/90 backdrop-blur-sm dark:border-gray-800 dark:bg-gray-950/90">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">

            {{-- Logo & Nama --}}
            <a href="{{ route('beranda') }}" class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600">
                    <x-heroicon-s-server-stack class="h-5 w-5 text-white" />
                </div>
                <span class="text-lg font-bold tracking-tight text-indigo-700 dark:text-indigo-400">SinauSubnet</span>
            </a>

            {{-- Nav Links Desktop --}}
            <nav class="hidden items-center gap-6 md:flex">
                <a href="{{ route('beranda') }}"
                   class="text-sm font-medium transition-colors {{ request()->routeIs('beranda') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100' }}">
                    Beranda
                </a>
                <a href="{{ route('tentang') }}"
                   class="text-sm font-medium transition-colors {{ request()->routeIs('tentang') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100' }}">
                    Tentang & Fitur
                </a>
            </nav>

            {{-- Tombol Auth --}}
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">
                        <x-heroicon-o-home class="h-4 w-4" />
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="text-sm font-medium text-gray-600 transition hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">
                        <x-heroicon-o-user-plus class="h-4 w-4" />
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- ── Konten Halaman ──────────────────────────────────────────────────── --}}
    <main>
        {{ $slot }}
    </main>

    {{-- ── Footer ──────────────────────────────────────────────────────────── --}}
    <footer class="mt-20 border-t border-gray-200 bg-gray-50 py-10 dark:border-gray-800 dark:bg-gray-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                <div class="flex items-center gap-2">
                    <div class="flex h-6 w-6 items-center justify-center rounded bg-indigo-600">
                        <x-heroicon-s-server-stack class="h-4 w-4 text-white" />
                    </div>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">SinauSubnet</span>
                </div>
                <p class="text-center text-xs text-gray-400">
                    Dibuat untuk penelitian skripsi — SMK Negeri · Tahun Ajaran 2026/2027
                </p>
                <p class="text-xs text-gray-400">
                    Laravel {{ app()->version() }} · PHP {{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }}
                </p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
