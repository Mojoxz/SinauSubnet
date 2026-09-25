<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Guru</h1>
        <p class="text-gray-500 mt-2">Ringkasan performa kelas dan antrean pekerjaan Anda.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <x-domain.stat-card 
            ikon="users" 
            warna="blue" 
            judul="Total Murid Aktif" 
            angka="{{ $totalMurid }}" 
            subteks="Terdaftar dalam sistem" 
        />
        
        <x-domain.stat-card 
            ikon="academic-cap" 
            warna="indigo" 
            judul="Rata-rata Kognitif" 
            angka="{{ $rataKognitifKelas }}%" 
            subteks="Nilai gabungan seluruh murid" 
        />
        
        <x-domain.stat-card 
            ikon="exclamation-circle" 
            warna="amber" 
            judul="Antrean Manual AI" 
            angka="{{ $totalAntreanReview }}" 
            subteks="Perlu review/koreksi manual" 
        />
        
        <x-domain.stat-card 
            ikon="clipboard-document-list" 
            warna="rose" 
            judul="Tunggu Psikomotorik" 
            angka="{{ $belumDinilaiPsiko }}" 
            subteks="Tugas praktikum belum dinilai" 
        />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Chart Analisis Performa Kelas -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <x-heroicon-o-chart-bar class="w-5 h-5 text-indigo-500" />
                Distribusi Nilai Rata-rata Kelas
            </h3>
            <div class="relative h-72 w-full">
                <canvas id="performaKelasChart"></canvas>
            </div>
        </div>

        <!-- Shortcut Tindakan -->
        <div class="space-y-4">
            <h3 class="font-bold text-gray-800 mb-2">Aksi Cepat</h3>
            
            <a href="{{ route('guru.penilaian.uraian') }}" wire:navigate class="block bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-xl p-5 transition-colors group">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="bg-amber-200 text-amber-700 w-12 h-12 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <x-heroicon-o-shield-exclamation class="w-6 h-6" />
                        </div>
                        <div>
                            <h4 class="font-bold text-amber-900">Review Koreksi AI ({{ $totalAntreanReview }})</h4>
                            <p class="text-sm text-amber-700 mt-1">Validasi skor ekstrem atau kegagalan sistem AI.</p>
                        </div>
                    </div>
                    <x-heroicon-o-chevron-right class="w-5 h-5 text-amber-400 group-hover:text-amber-600 transition-colors" />
                </div>
            </a>

            <a href="{{ route('guru.praktikum.index') }}" wire:navigate class="block bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl p-5 transition-colors group">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="bg-rose-200 text-rose-700 w-12 h-12 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <x-heroicon-o-clipboard-document-check class="w-6 h-6" />
                        </div>
                        <div>
                            <h4 class="font-bold text-rose-900">Penilaian Praktik ({{ $belumDinilaiPsiko }})</h4>
                            <p class="text-sm text-rose-700 mt-1">Isi rubrik psikomotorik dari unggahan Packet Tracer murid.</p>
                        </div>
                    </div>
                    <x-heroicon-o-chevron-right class="w-5 h-5 text-rose-400 group-hover:text-rose-600 transition-colors" />
                </div>
            </a>
            
            <a href="{{ route('guru.laporan.index') }}" wire:navigate class="block bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl p-5 transition-colors group">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="bg-emerald-200 text-emerald-700 w-12 h-12 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <x-heroicon-o-document-chart-bar class="w-6 h-6" />
                        </div>
                        <div>
                            <h4 class="font-bold text-emerald-900">Ekspor Laporan (Excel/PDF)</h4>
                            <p class="text-sm text-emerald-700 mt-1">Unduh rekapitulasi nilai akhir kelas dan laporan performa individu.</p>
                        </div>
                    </div>
                    <x-heroicon-o-chevron-right class="w-5 h-5 text-emerald-400 group-hover:text-emerald-600 transition-colors" />
                </div>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('livewire:navigated', function () {
        const ctx = document.getElementById('performaKelasChart');
        if(!ctx) return;
        
        const chartData = {!! $chartData !!};
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Rata-rata Kelas (%)',
                    data: chartData.data,
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.6)',   // C3
                        'rgba(59, 130, 246, 0.6)',   // C4
                        'rgba(14, 165, 233, 0.6)',   // C5
                        'rgba(16, 185, 129, 0.6)'    // Psiko
                    ],
                    borderColor: [
                        'rgb(99, 102, 241)',
                        'rgb(59, 130, 246)',
                        'rgb(14, 165, 233)',
                        'rgb(16, 185, 129)'
                    ],
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + '%';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
