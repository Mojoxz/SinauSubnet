<div>
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('murid.nilai.riwayat') }}" wire:navigate class="text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-2 font-medium text-sm">
            <x-heroicon-o-arrow-left class="w-4 h-4" />
            Kembali ke Riwayat
        </a>
    </div>

    <!-- Header Rekap -->
    <div class="bg-indigo-900 rounded-2xl shadow-lg border border-indigo-700 overflow-hidden mb-8 relative">
        <div class="absolute right-0 top-0 w-64 h-64 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 transform translate-x-1/3 -translate-y-1/2"></div>
        <div class="relative z-10 p-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="text-center md:text-left">
                <h1 class="text-3xl font-bold text-white mb-2">{{ $hasilQuiz->quiz->judul }}</h1>
                <p class="text-indigo-200">Diselesaikan pada: {{ \Carbon\Carbon::parse($hasilQuiz->waktu_selesai)->translatedFormat('l, d M Y H:i') }}</p>
            </div>
            
            <div class="flex gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-6 py-4 text-center border border-white/20">
                    <span class="block text-xs font-bold text-indigo-200 uppercase tracking-wider mb-1">Skor Total</span>
                    <span class="text-3xl font-black text-white font-mono">{{ $hasilQuiz->skor_total }} <span class="text-lg text-indigo-300">/ {{ $hasilQuiz->quiz->soalQuizzes->sum('skor_maks') }}</span></span>
                </div>
                <div class="bg-amber-500/10 backdrop-blur-sm rounded-xl px-6 py-4 text-center border border-amber-500/30">
                    <span class="block text-xs font-bold text-amber-200 uppercase tracking-wider mb-1">Poin Gamifikasi</span>
                    <span class="text-3xl font-black text-amber-400 font-mono">+{{ $hasilQuiz->poin_total }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Soal dan Pembahasan -->
    <div class="space-y-6">
        @foreach($hasilQuiz->quiz->soalQuizzes as $index => $soal)
            @php
                $jawaban = $jawabanList->get($soal->id);
                $status = $jawaban ? \App\Enums\StatusPenilaian::tryFrom($jawaban->status_penilaian) : null;
            @endphp
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <span class="bg-indigo-600 text-white w-6 h-6 flex items-center justify-center rounded-full text-xs">{{ $index + 1 }}</span>
                        Soal {{ str_replace('_', ' ', Str::title($soal->tipe->value)) }}
                    </h3>
                    
                    <div class="flex items-center gap-4">
                        @if($status)
                            <x-domain.status-badge :status="$status" />
                        @endif
                        <span class="text-sm font-bold {{ ($jawaban->skor_final ?? 0) > 0 ? 'text-emerald-600' : 'text-gray-500' }}">
                            {{ $jawaban->skor_final ?? 0 }} / {{ $soal->skor_maks }} Poin
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="prose prose-sm max-w-none text-gray-800 mb-6">
                        {!! Str::markdown($soal->pertanyaan) !!}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Jawaban Anda -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Jawaban Anda</h4>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-sm font-mono text-gray-800 whitespace-pre-wrap">{{ $jawaban->jawaban ?? '(Tidak dijawab)' }}</p>
                            </div>
                        </div>

                        <!-- Kunci Jawaban / Feedback -->
                        <div>
                            @if($soal->tipe->value === 'uraian')
                                <h4 class="text-xs font-bold text-indigo-500 uppercase tracking-wider mb-2">Analisis & Umpan Balik</h4>
                                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 space-y-3">
                                    @if($jawaban->status_penilaian === 'menunggu_ai')
                                        <p class="text-sm text-gray-600 italic">Sedang dalam antrean penilaian otomatis (AI)...</p>
                                    @elseif($jawaban->status_penilaian === 'perlu_manual')
                                        <p class="text-sm text-red-600 italic">Sistem AI tidak dapat mengevaluasi. Menunggu penilaian manual dari guru.</p>
                                    @else
                                        <p class="text-sm text-gray-700 italic">"{{ $jawaban->feedback_final ?? $jawaban->feedback_ai }}"</p>
                                        
                                        @if($jawaban->detail_skor_ai && in_array($jawaban->status_penilaian, ['dinilai_ai', 'divalidasi_guru']))
                                            <div class="mt-3 pt-3 border-t border-indigo-200/50">
                                                <p class="text-[10px] font-bold text-indigo-800 uppercase tracking-wider mb-2">Rincian Penilaian Kriteria</p>
                                                @foreach($jawaban->detail_skor_ai as $kriteria => $skorK)
                                                    <div class="flex justify-between text-xs py-1">
                                                        <span class="text-gray-600">{{ $kriteria }}</span>
                                                        <span class="font-bold text-gray-900 font-mono">{{ $skorK }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @else
                                <h4 class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-2">Kunci Jawaban</h4>
                                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                                    @php
                                        // Deteksi apakah format kunci_jawaban berisi data struktur subnet
                                        $kunci = $soal->kunci_jawaban ?? [];
                                        $isSubnetFormat = isset($kunci[0]) && is_array($kunci[0]) && array_key_exists('network', $kunci[0]);
                                    @endphp
                                    
                                    @if($isSubnetFormat)
                                        <x-domain.tabel-subnet :subnets="$kunci" />
                                    @else
                                        <ul class="list-disc list-inside text-sm font-mono text-emerald-800">
                                            @foreach($kunci as $k)
                                                <li>{{ is_array($k) ? json_encode($k) : $k }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($soal->pembahasan)
                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pembahasan Materi</h4>
                            <div class="prose prose-sm max-w-none text-gray-600 bg-gray-50 rounded-lg p-4">
                                {!! Str::markdown($soal->pembahasan) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
