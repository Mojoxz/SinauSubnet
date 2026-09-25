{{--
    Komponen: <x-domain.card>

    Kartu konten generik dengan padding, shadow, dan border radius konsisten.
    Dipakai di seluruh halaman sebagai pembungkus konten panel/section.

    Props:
      - $class : string — class tambahan (opsional)

    Slot:
      - $slot  : konten kartu
--}}
@props(['class' => ''])

<div {{ $attributes->class(['rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900', $class]) }}>
    {{ $slot }}
</div>
