{{--
    Komponen: <x-domain.status-badge>

    Menampilkan badge berwarna semantik berdasarkan StatusPenilaian enum.
    Warna didefinisikan satu kali di App\Enums\StatusPenilaian::badgeClass().

    Props:
      - $status : App\Enums\StatusPenilaian — objek enum status penilaian
      - $size   : string — 'sm' | 'md' (default: 'sm')
--}}
@props([
    'status',
    'size' => 'sm',
])

@php
    $baseClass  = 'inline-flex items-center gap-1 rounded-full border font-medium leading-none';
    $sizeClass  = $size === 'md' ? 'px-3 py-1.5 text-sm' : 'px-2.5 py-1 text-xs';
    $colorClass = $status instanceof \App\Enums\StatusPenilaian
        ? $status->badgeClass()
        : 'bg-gray-100 text-gray-800 border-gray-300';
@endphp

<span {{ $attributes->class([$baseClass, $sizeClass, $colorClass]) }}>
    @if ($status instanceof \App\Enums\StatusPenilaian)
        {{ $status->label() }}
    @else
        {{ $status }}
    @endif
</span>
