{{--
    Komponen: <x-domain.alamat-ip>

    Menampilkan alamat IP per-oktet menggunakan font IBM Plex Mono,
    dengan tombol toggle untuk menampilkan representasi biner 8-bit per oktet.

    Props:
      - $ip      : string — alamat IP, mis. "192.168.10.0"
      - $label   : string|null — label opsional di atas IP
      - $showBin : bool — tampilkan biner secara default (default: false)
--}}
@props([
    'ip',
    'label'   => null,
    'showBin' => false,
])

@php
    $oktets   = explode('.', $ip);
    $binerMap = array_map(fn($o) => str_pad(decbin((int) $o), 8, '0', STR_PAD_LEFT), $oktets);
    $id       = 'ip-' . md5($ip . uniqid());
@endphp

<div {{ $attributes->class(['inline-block']) }}>
    @if ($label)
        <p class="mb-0.5 text-xs font-medium text-gray-500">{{ $label }}</p>
    @endif

    <div x-data="{ showBiner: {{ $showBin ? 'true' : 'false' }} }" class="font-mono text-sm">
        {{-- Tampilan desimal --}}
        <div x-show="!showBiner" class="flex items-center gap-0.5 text-gray-900 dark:text-gray-100">
            @foreach ($oktets as $i => $oktet)
                <span class="rounded bg-gray-100 px-1.5 py-0.5 dark:bg-gray-800">{{ $oktet }}</span>
                @if (! $loop->last)
                    <span class="text-gray-400">.</span>
                @endif
            @endforeach
        </div>

        {{-- Tampilan biner --}}
        <div x-show="showBiner" class="flex items-center gap-0.5 text-indigo-700 dark:text-indigo-300">
            @foreach ($binerMap as $i => $biner)
                <span class="rounded bg-indigo-50 px-1.5 py-0.5 tracking-wider dark:bg-indigo-900/30">{{ $biner }}</span>
                @if (! $loop->last)
                    <span class="text-gray-400">.</span>
                @endif
            @endforeach
        </div>

        {{-- Tombol toggle --}}
        <button
            type="button"
            x-on:click="showBiner = !showBiner"
            class="mt-1 text-xs text-indigo-600 hover:underline dark:text-indigo-400"
        >
            <span x-show="!showBiner">Tampilkan Biner</span>
            <span x-show="showBiner">Tampilkan Desimal</span>
        </button>
    </div>
</div>
