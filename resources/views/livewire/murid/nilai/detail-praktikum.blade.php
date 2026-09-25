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
                <h1 class="text-3xl font-bold text-white mb-2">{{ $penilaianPraktikum->praktikum->judul }}</h1>
                <p class="text-indigo-200">Dinilai oleh Guru: {{ $penilaianPraktikum->guru->user->name ?? '-' }}</p>
            </div>
            
            <div class="flex gap-4">
                @php
                    $skorMaksPsiko = $penilaianPraktikum->nilaiAspeks->count() * 4;
                    $skorMuridPsiko = $penilaianPraktikum->nilaiAspeks->sum('skor');
                    $nilaiPsiko = $skorMaksPsiko > 0 ? round(($skorMuridPsiko / $skorMaksPsiko) * 100) : 0;
                @endphp
                <div class="bg-white/10 backdrop-blur-sm rounded-xl px-6 py-4 text-center border border-white/20">
                    <span class="block text-xs font-bold text-emerald-200 uppercase tracking-wider mb-1">Nilai Praktik (Psikomotorik)</span>
                    <span class="text-3xl font-black text-white font-mono">{{ $nilaiPsiko }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Guru (Psikomotorik) -->
    @if($penilaianPraktikum->catatan)
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-r-xl p-6 mb-8 shadow-sm">
            <h3 class="font-bold text-blue-900 flex items-center gap-2 mb-2">
                <x-heroicon-o-chat-bubble-bottom-center-text class="w-5 h-5" />
                Catatan Guru (Kinerja Praktik)
            </h3>
            <p class="text-blue-800 text-sm">{{ $penilaianPraktikum->catatan }}</p>
        </div>
    @endif

    <!-- Rubrik Kinerja Praktik -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <x-heroicon-o-clipboard-document-check class="w-5 h-5 text-gray-500" />
                Rincian Rubrik Penilaian Kinerja Praktik
            </h3>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3 w-16">No</th>
                    <th class="px-6 py-3">Aspek Penilaian</th>
                    <th class="px-6 py-3 text-center w-32">Skor (1-4)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @foreach($penilaianPraktikum->nilaiAspeks as $index => $nilaiAspek)
                    <tr>
                        <td class="px-6 py-4 text-gray-500 text-center">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $nilaiAspek->rubrikPraktikum->aspek }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $nilaiAspek->rubrikPraktikum->indikator }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block w-8 h-8 leading-8 rounded-full font-bold {{ $nilaiAspek->skor >= 3 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $nilaiAspek->skor }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Analisis Soal & Jawaban Praktikum -->
    <h3 class="font-bold text-gray-900 text-xl mb-4">Laporan Analisis & Evaluasi Studi Kasus (Kognitif)</h3>
    <div class="space-y-6">
        @foreach($penilaianPraktikum->praktikum->soalPraktikums as $index => $soal)
            @php
                $jawaban = $jawabanList->get($soal->id);
                $status = $jawaban ? \App\Enums\StatusPenilaian::tryFrom($jawaban->status_penilaian) : null;
            @endphp
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h4 class="font-bold text-gray-800 flex items-center gap-2">
                        <span class="bg-indigo-600 text-white w-6 h-6 flex items-center justify-center rounded-full text-xs">{{ $index + 1 }}</span>
                        Soal {{ str_replace('_', ' ', Str::title($soal->tipe->value)) }}
                    </h4>
                    
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
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Jawaban Analisis Anda</h4>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-sm font-mono text-gray-800 whitespace-pre-wrap">{{ $jawaban->jawaban ?? '(Tidak dijawab)' }}</p>
                                
                                @if($jawaban && $jawaban->file_bukti_path)
                                    <div class="mt-4 pt-4 border-t border-blue-200 flex justify-between items-center">
                                        <div class="flex items-center gap-2 text-blue-700 text-xs font-medium">
                                            <x-heroicon-o-paper-clip class="w-4 h-4" />
                                            Berisi lampiran berkas Praktik
                                        </div>
                                        <a href="{{ route('storage.bukti', $jawaban->id) }}" target="_blank" class="bg-blue-600 text-white hover:bg-blue-700 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors shadow-sm">
                                            Unduh Lampiran
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Kunci Jawaban / Feedback -->
                        <div>
                            @if($soal->tipe->value === 'uraian')
                                <h4 class="text-xs font-bold text-indigo-500 uppercase tracking-wider mb-2">Analisis & Umpan Balik Kognitif</h4>
                                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 space-y-3">
                                    @if($jawaban->status_penilaian === 'menunggu_ai')
                                        <p class="text-sm text-gray-600 italic">Sedang dalam antrean penilaian otomatis (AI)...</p>
                                    @elseif($jawaban->status_penilaian === 'perlu_manual')
                                        <p class="text-sm text-red-600 italic">Sistem AI tidak dapat mengevaluasi secara kognitif. Menunggu penilaian manual dari guru.</p>
                                    @else
                                        <p class="text-sm text-gray-700 italic">"{{ $jawaban->feedback_final ?? $jawaban->feedback_ai }}"</p>
                                        
                                        @if($jawaban->detail_skor_ai && in_array($jawaban->status_penilaian, ['dinilai_ai', 'divalidasi_guru']))
                                            <div class="mt-3 pt-3 border-t border-indigo-200/50">
                                                <p class="text-[10px] font-bold text-indigo-800 uppercase tracking-wider mb-2">Rincian Penilaian Kriteria AI</p>
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
