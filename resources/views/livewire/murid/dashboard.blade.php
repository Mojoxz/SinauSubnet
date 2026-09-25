<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Selamat Datang, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-500 mt-2">Lanjutkan perjalanan belajar jaringan komputermu hari ini.</p>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <x-domain.stat-card 
            ikon="bolt" 
            warna="amber" 
            judul="Total Poin Gamifikasi" 
            angka="{{ number_format($totalPoin, 0, ',', '.') }}" 
            subteks="Kumpulkan terus poinnya!" 
        />
        
        <x-domain.stat-card 
            ikon="check-badge" 
            warna="emerald" 
            judul="Badge Terbaru" 
            angka="{{ $badgeTerbaru ? Str::limit($badgeTerbaru->nama, 15) : 'Belum Ada' }}" 
            subteks="{{ $badgeTerbaru ? 'Diperoleh ' . \Carbon\Carbon::parse($badgeTerbaru->pivot->diperoleh_pada)->diffForHumans() : 'Selesaikan modul untuk badge' }}" 
        />
        
        <x-domain.stat-card 
            ikon="book-open" 
            warna="blue" 
            judul="Materi Selesai" 
            angka="{{ $materiSelesai }}" 
            subteks="Dari total {{ $materiAktif->count() }} materi aktif" 
        />
        
        <x-domain.stat-card 
            ikon="academic-cap" 
            warna="indigo" 
            judul="Rata-rata Kognitif" 
            angka="{{ $skorRataRata }}%" 
            subteks="Gabungan skor C3, C4, dan C5" 
        />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="xl:col-span-2 space-y-8">
            <!-- Rekomendasi Modul Selanjutnya -->
            @if($rekomendasiMateri)
                <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl p-8 text-white shadow-lg relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-64 h-64 bg-white rounded-full mix-blend-overlay filter blur-3xl opacity-20 transform translate-x-1/2 -translate-y-1/2"></div>
                    
                    <div class="relative z-10">
                        <span class="bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider backdrop-blur-sm border border-white/30">Lanjutkan Belajar</span>
                        <h2 class="text-2xl font-bold mt-4 mb-2">{{ $rekomendasiMateri['materi']->judul }}</h2>
                        <p class="text-indigo-100 mb-6">Tahap saat ini: <strong class="text-white uppercase">{{ $rekomendasiMateri['statusPbl'] }}</strong></p>
                        
                        @php
                            $routeAction = 'murid.materi.baca';
                            $params = $rekomendasiMateri['materi']->id;
                            
                            if ($rekomendasiMateri['statusPbl'] === 'praktikum') {
                                // Cari id praktikum dari materi
                                $praktikumId = $rekomendasiMateri['materi']->praktikums->first()->id ?? null;
                                if ($praktikumId) {
                                    $routeAction = 'murid.praktikum.kerjakan';
                                    $params = $praktikumId;
                                }
                            } elseif ($rekomendasiMateri['statusPbl'] === 'quiz') {
                                $quizId = $rekomendasiMateri['materi']->quizzes->first()->id ?? null;
                                if ($quizId) {
                                    $routeAction = 'murid.quiz.mulai';
                                    $params = $quizId;
                                }
                            }
                        @endphp
                        
                        <a href="{{ route($routeAction, $params) }}" wire:navigate class="inline-flex items-center justify-center gap-2 bg-white text-indigo-700 hover:bg-gray-50 px-6 py-3 rounded-xl font-bold transition-colors shadow-sm">
                            <x-heroicon-s-play class="w-5 h-5" />
                            Lanjutkan Modul Ini
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-8 text-center text-emerald-800 shadow-sm">
                    <x-heroicon-s-check-circle class="w-16 h-16 text-emerald-400 mx-auto mb-4" />
                    <h2 class="text-2xl font-bold mb-2">Luar Biasa!</h2>
                    <p>Anda telah menyelesaikan seluruh modul pembelajaran PBL.</p>
                </div>
            @endif

            <!-- Progress Pembelajaran Berjenjang (PBL) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-heroicon-o-map class="w-5 h-5 text-gray-500" />
                        Peta Kemajuan Pembelajaran Berbasis Masalah (PBL)
                    </h3>
                </div>
                
                <div class="divide-y divide-gray-100">
                    @foreach($materiAktif as $materi)
                        @php
                            $status = $statusPblArray[$materi->id]['status'];
                            $isLocked = $statusPblArray[$materi->id]['isLocked'];
                        @endphp
                        <div class="p-6 {{ $isLocked ? 'opacity-50 grayscale' : '' }}">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-bold text-gray-900 flex items-center gap-2">
                                    @if($status === 'selesai')
                                        <x-heroicon-s-check-circle class="w-5 h-5 text-emerald-500" />
                                    @elseif($isLocked)
                                        <x-heroicon-s-lock-closed class="w-5 h-5 text-gray-400" />
                                    @else
                                        <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                                    @endif
                                    Level {{ $materi->level }}: {{ $materi->judul }}
                                </h4>
                                @if($isLocked)
                                    <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded">Terkunci</span>
                                @endif
                            </div>
                            
                            <!-- Stepper Tahap PBL Component -->
                            <x-domain.tahap-pbl :materiId="$materi->id" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar Kanan -->
        <div class="space-y-6">
            <!-- Papan Peringkat Mini -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-heroicon-o-trophy class="w-5 h-5 text-amber-500" />
                        Top 5 Leaderboard
                    </h3>
                    <a href="{{ route('murid.leaderboard') }}" wire:navigate class="text-xs text-indigo-600 font-bold hover:underline">Lihat Semua</a>
                </div>
                <div class="p-0">
                    @php
                        $topMurids = \App\Models\Murid::with('user')->orderBy('total_poin', 'desc')->orderBy('poin_dicapai_pada', 'asc')->take(5)->get();
                    @endphp
                    <ul class="divide-y divide-gray-100">
                        @foreach($topMurids as $index => $tm)
                            <li class="px-6 py-3 flex items-center justify-between {{ $tm->id === auth()->user()->murid->id ? 'bg-indigo-50' : '' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $index === 0 ? 'bg-amber-100 text-amber-700' : ($index === 1 ? 'bg-gray-200 text-gray-700' : ($index === 2 ? 'bg-orange-100 text-orange-800' : 'text-gray-400')) }}">
                                        {{ $index + 1 }}
                                    </div>
                                    <span class="font-medium text-sm text-gray-800">{{ Str::limit($tm->user->name, 15) }}</span>
                                </div>
                                <span class="font-mono text-sm font-bold text-gray-900">{{ number_format($tm->total_poin, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            
            <!-- Panduan Bantuan Singkat -->
            <div class="bg-blue-50 rounded-2xl border border-blue-200 p-6">
                <h3 class="font-bold text-blue-900 mb-2">Panduan Tahap PBL</h3>
                <ul class="space-y-3 text-sm text-blue-800">
                    <li class="flex gap-2">
                        <x-heroicon-o-book-open class="w-5 h-5 shrink-0" />
                        <span><strong>Materi:</strong> Pahami konsep dasar dan skenario permasalahan.</span>
                    </li>
                    <li class="flex gap-2">
                        <x-heroicon-o-beaker class="w-5 h-5 shrink-0" />
                        <span><strong>Praktikum:</strong> Analisis skenario dan unggah file simulasi Cisco Packet Tracer.</span>
                    </li>
                    <li class="flex gap-2">
                        <x-heroicon-o-document-check class="w-5 h-5 shrink-0" />
                        <span><strong>Quiz Evaluasi:</strong> Uji pemahaman Anda dengan batas waktu.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
