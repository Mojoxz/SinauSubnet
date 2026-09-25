{{--
    Komponen: <x-domain.tahap-pbl>

    Stepper visual 3 tahap PBL: Materi → Praktikum → Quiz.
    Menggunakan ikon SVG Heroicons — TIDAK memakai emoji.

    Props:
      - $persen_materi    : int — 0-100
      - $persen_praktikum : int — 0-100
      - $persen_quiz      : int — 0-100
      - $compact          : bool — tampilan ringkas tanpa persentase
--}}
@props([
    'persen_materi'    => 0,
    'persen_praktikum' => 0,
    'persen_quiz'      => 0,
    'compact'          => false,
])

@php
    $tahaps = [
        [
            'label'  => 'Materi',
            'persen' => $persen_materi,
            'icon'   => 'book-open',
        ],
        [
            'label'  => 'Praktikum',
            'persen' => $persen_praktikum,
            'icon'   => 'wrench-screwdriver',
        ],
        [
            'label'  => 'Quiz',
            'persen' => $persen_quiz,
            'icon'   => 'pencil-square',
        ],
    ];

    $ringClass = fn(int $p) => match (true) {
        $p >= 100 => 'bg-emerald-500 text-white ring-emerald-200',
        $p > 0    => 'bg-amber-400 text-white ring-amber-200',
        default   => 'bg-gray-100 text-gray-400 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700',
    };

    $statusLabel = fn(int $p) => match (true) {
        $p >= 100 => 'Selesai',
        $p > 0    => 'Sedang',
        default   => 'Belum',
    };
@endphp

<div {{ $attributes->class(['flex items-center w-full']) }}>
    @foreach ($tahaps as $tahap)
        <div class="flex flex-col items-center">
            {{-- Lingkaran ikon tahap --}}
            <div class="flex h-9 w-9 items-center justify-center rounded-full ring-2 {{ $ringClass($tahap['persen']) }}">
                @if ($tahap['persen'] >= 100)
                    {{-- Ikon centang saat selesai --}}
                    <x-heroicon-s-check class="h-5 w-5" />
                @else
                    <x-dynamic-component :component="'heroicon-o-' . $tahap['icon']" class="h-5 w-5" />
                @endif
            </div>

            @unless ($compact)
                <div class="mt-1 text-center">
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $tahap['label'] }}</p>
                    <p class="text-xs text-gray-400">{{ $statusLabel($tahap['persen']) }} ({{ $tahap['persen'] }}%)</p>
                </div>
            @endunless
        </div>

        {{-- Garis penghubung --}}
        @unless ($loop->last)
            <div class="mx-1 h-1 flex-1 rounded-full {{ $tahap['persen'] >= 100 ? 'bg-emerald-400' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
        @endunless
    @endforeach
</div>
