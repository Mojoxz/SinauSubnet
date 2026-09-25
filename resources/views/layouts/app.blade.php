<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SinauSubnet' }} — {{ auth()->user()?->isGuru() ? 'Area Guru' : 'Area Murid' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 font-sans antialiased dark:bg-gray-950">

    {{-- ── Sidebar Desktop ─────────────────────────────────────────────────── --}}
    <div class="flex h-screen overflow-hidden">

        <aside class="hidden w-64 flex-shrink-0 flex-col border-r border-gray-200 bg-white lg:flex dark:border-gray-800 dark:bg-gray-900">

            {{-- Logo --}}
            <div class="flex h-16 items-center gap-2.5 border-b border-gray-200 px-5 dark:border-gray-800">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600">
                    <x-heroicon-s-server-stack class="h-5 w-5 text-white" />
                </div>
                <span class="text-base font-bold text-indigo-700 dark:text-indigo-400">SinauSubnet</span>
            </div>

            {{-- Profil singkat --}}
            <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400">
                    @if(auth()->user()->isGuru()) Guru @else {{ auth()->user()->murid?->kelas ?? 'Murid' }} @endif
                </p>
            </div>

            {{-- Navigasi --}}
            <nav class="flex-1 overflow-y-auto px-3 py-4">
                <x-sidebar />
            </nav>

            {{-- Logout --}}
            <div class="border-t border-gray-200 p-3 dark:border-gray-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white">
                        <x-heroicon-o-arrow-right-on-rectangle class="h-4 w-4" />
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- ── Konten Utama ─────────────────────────────────────────────────── --}}
        <div class="flex flex-1 flex-col overflow-hidden">

            {{-- Topbar --}}
            <header class="flex h-16 flex-shrink-0 items-center justify-between border-b border-gray-200 bg-white px-4 shadow-sm sm:px-6 dark:border-gray-800 dark:bg-gray-900">
                <div>
                    <h1 class="text-base font-semibold text-gray-900 dark:text-white">{{ $title ?? 'Dashboard' }}</h1>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Notification Bell --}}
                    <livewire:navigation-notification-bell />

                    {{-- Avatar --}}
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
            </header>

            {{-- Main scroll area --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
