{{--
    Komponen: <x-domain.tabel-subnet>

    Menampilkan tabel terstruktur hasil perhitungan subnetting:
    Network Address, Usable Host Range, Broadcast Address, dan Subnet Mask.

    Props:
      - $subnets : array — daftar subnet, masing-masing berupa array asosiatif:
          [
            'label'      => 'Lab 1',          // opsional
            'network'    => '192.168.10.0',
            'mask'       => '/27',
            'host_min'   => '192.168.10.1',
            'host_max'   => '192.168.10.30',
            'broadcast'  => '192.168.10.31',
            'jumlah_host'=> 30,               // opsional
          ]
--}}
@props([
    'subnets' => [],
])

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 font-mono text-sm dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                @if (collect($subnets)->contains(fn($s) => isset($s['label'])))
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Segmen</th>
                @endif
                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Network Address</th>
                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Subnet Mask</th>
                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Host Pertama</th>
                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Host Terakhir</th>
                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Broadcast</th>
                @if (collect($subnets)->contains(fn($s) => isset($s['jumlah_host'])))
                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Host</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
            @forelse ($subnets as $subnet)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    @if (collect($subnets)->contains(fn($s) => isset($s['label'])))
                        <td class="px-3 py-2 font-sans text-xs font-medium text-gray-700 dark:text-gray-300">
                            {{ $subnet['label'] ?? '—' }}
                        </td>
                    @endif
                    <td class="px-3 py-2 text-indigo-700 dark:text-indigo-300">{{ $subnet['network'] ?? '—' }}</td>
                    <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $subnet['mask'] ?? '—' }}</td>
                    <td class="px-3 py-2 text-emerald-700 dark:text-emerald-300">{{ $subnet['host_min'] ?? '—' }}</td>
                    <td class="px-3 py-2 text-emerald-700 dark:text-emerald-300">{{ $subnet['host_max'] ?? '—' }}</td>
                    <td class="px-3 py-2 text-rose-700 dark:text-rose-300">{{ $subnet['broadcast'] ?? '—' }}</td>
                    @if (collect($subnets)->contains(fn($s) => isset($s['jumlah_host'])))
                        <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-300">{{ $subnet['jumlah_host'] ?? '—' }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-3 py-4 text-center font-sans text-xs text-gray-400">Tidak ada data subnet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
