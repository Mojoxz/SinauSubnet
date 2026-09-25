{{--
    Komponen: <x-domain.stat-card>

    Kartu ringkasan statistik Dashboard. Menggunakan slot ikon berupa
    komponen Heroicons — TIDAK memakai emoji.

    Props:
      - $judul   : string
      - $nilai   : string|int
      - $subTeks : string|null
      - $warna   : 'biru'|'hijau'|'amber'|'merah'|'ungu'|'abu'

    Slot:
      - $ikon    : konten ikon (gunakan <x-heroicon-o-xxx class="h-5 w-5" />)
--}}
@props([
    'judul'   => '',
    'nilai'   => '—',
    'subTeks' => null,
    'warna'   => 'biru',
])

@php
    $warnaMap = [
        'biru'  => 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
        'hijau' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
        'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
        'merah' => 'bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400',
        'ungu'  => 'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
        'abu'   => 'bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
    ];
    $ikonClass = $warnaMap[$warna] ?? $warnaMap['biru'];
@endphp

<div class="flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
    {{-- Area ikon — gunakan slot dengan <x-heroicon-o-xxx class="h-5 w-5" /> --}}
    <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg {{ $ikonClass }}">
        {{ $ikon ?? '' }}
    </div>

    {{-- Konten --}}
    <div class="min-w-0 flex-1">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $judul }}</p>
        <p class="mt-0.5 text-2xl font-bold text-gray-900 dark:text-white">{{ $nilai }}</p>
        @if ($subTeks)
            <p class="mt-0.5 text-xs text-gray-400">{{ $subTeks }}</p>
        @endif
    </div>
</div>
