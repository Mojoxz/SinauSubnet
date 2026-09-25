<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Riwayat Nilai & Evaluasi</h1>
        <p class="text-gray-500 mt-2">Pantau perkembangan kompetensi Anda pada ranah kognitif dan psikomotorik.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Radar Chart Kinerja -->
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <x-heroicon-o-chart-pie class="w-5 h-5 text-indigo-500" />
                Peta Kompetensi (Radar)
            </h3>
            <div class="relative h-64 w-full flex justify-center">
                <canvas id="kompetensiRadarChart"></canvas>
            </div>
            <p class="text-xs text-gray-500 text-center mt-4">Analisis performa Anda berdasarkan klasifikasi level kognitif Bloom dan kinerja praktik.</p>
        </div>

        <!-- Tabel Riwayat Evaluasi -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-gray-500" />
                    Riwayat Ujian & Praktikum
                </h3>
                <select wire:model.live="filterTipe" class="border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="semua">Semua Evaluasi</option>
                    <option value="quiz">Kuis Saja</option>
                    <option value="praktikum">Praktikum Saja</option>
                </select>
            </div>
            
            <div class="overflow-y-auto max-h-96">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-white sticky top-0 border-b border-gray-200 z-10">
                        <tr class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Modul / Materi</th>
                            <th class="px-6 py-3">Jenis</th>
                            <th class="px-6 py-3 text-center">Skor / Nilai</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($riwayat as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">
                                        {{ $item->jenis === 'quiz' ? $item->quiz->judul : $item->praktikum->judul }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Diselesaikan: {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y, H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($item->jenis === 'quiz')
                                        <span class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 text-xs font-bold px-2 py-1 rounded border border-indigo-200 uppercase tracking-wider">
                                            <x-heroicon-o-document-check class="w-3 h-3" /> Kuis Kognitif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-xs font-bold px-2 py-1 rounded border border-emerald-200 uppercase tracking-wider">
                                            <x-heroicon-o-beaker class="w-3 h-3" /> Praktikum
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($item->jenis === 'quiz')
                                        <span class="font-mono font-bold text-gray-900">{{ $item->skor_total }}</span>
                                        <span class="text-xs text-gray-500">/ {{ $item->quiz->soalQuizzes->sum('skor_maks') }}</span>
                                    @else
                                        @php
                                            $skorMaks = $item->nilaiAspeks->count() * 4;
                                            $skor = $item->nilaiAspeks->sum('skor');
                                            $nilai = $skorMaks > 0 ? round(($skor / $skorMaks) * 100) : 0;
                                        @endphp
                                        <span class="font-mono font-bold text-gray-900">{{ $nilai }}%</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ $item->jenis === 'quiz' ? route('nilai.detail-quiz', $item->id) : route('nilai.detail-praktikum', $item->id) }}" wire:navigate class="inline-flex items-center gap-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg text-xs font-medium shadow-sm transition-colors">
                                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                                        Detail Review
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <x-heroicon-o-folder-open class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                                    <p class="font-medium">Belum ada riwayat evaluasi yang diselesaikan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('livewire:navigated', function () {
        const ctx = document.getElementById('kompetensiRadarChart');
        if(!ctx) return;
        
        const chartData = {!! $chartData !!};
        
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Skor Anda (%)',
                    data: chartData.data,
                    backgroundColor: 'rgba(99, 102, 241, 0.2)',
                    borderColor: 'rgb(99, 102, 241)',
                    pointBackgroundColor: 'rgb(99, 102, 241)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(99, 102, 241)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: {
                            display: true
                        },
                        suggestedMin: 0,
                        suggestedMax: 100,
                        ticks: {
                            stepSize: 20
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
