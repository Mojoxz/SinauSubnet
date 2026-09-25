<div>
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Review Koreksi AI (Uraian)</h1>
            <p class="text-gray-500 text-sm mt-1">Daftar jawaban uraian yang membutuhkan validasi atau dikoreksi manual karena skor ekstrem atau sampel penelitian.</p>
        </div>
        <div class="flex gap-3">
            <select wire:model.live="filterTipe" class="border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="semua">Semua Modul</option>
                <option value="quiz">Kuis Evaluasi</option>
                <option value="praktikum">Praktikum</option>
            </select>
            <select wire:model.live="filterStatus" class="border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="butuh_perhatian">Butuh Perhatian (Unreviewed / Manual)</option>
                <option value="divalidasi_guru">Sudah Direview Guru</option>
                <option value="semua">Semua Status</option>
            </select>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-sm flex items-center gap-3">
            <x-heroicon-o-check-circle class="w-5 h-5" />
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-4">Siswa & Modul</th>
                    <th class="px-6 py-4">Status & Flag</th>
                    <th class="px-6 py-4 text-center">Skor AI</th>
                    <th class="px-6 py-4 text-center">Skor Final</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($daftarJawaban as $jawaban)
                    @php
                        $isQuiz = $jawaban->jenis === 'quiz';
                        $murid = $isQuiz ? $jawaban->hasilQuiz->murid : $jawaban->murid;
                        $soal = $isQuiz ? $jawaban->soalQuiz : $jawaban->soalPraktikum;
                        $judulModul = $isQuiz ? $soal->quiz->judul : $soal->praktikum->judul;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $murid->user->name }} <span class="text-xs text-gray-500 font-normal">({{ $murid->kelas }})</span></div>
                            <div class="text-xs text-indigo-600 mt-1 flex items-center gap-1">
                                @if($isQuiz) <x-heroicon-o-document-check class="w-3 h-3" /> @else <x-heroicon-o-server-stack class="w-3 h-3" /> @endif
                                {{ $judulModul }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <x-domain.status-badge :status="\App\Enums\StatusPenilaian::tryFrom($jawaban->status_penilaian)" />
                            
                            @if($jawaban->sampel_review)
                                <div class="mt-2 inline-flex items-center gap-1 bg-purple-100 text-purple-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider border border-purple-200" title="Terpilih permanen sebagai sampel uji penelitian">
                                    <x-heroicon-s-beaker class="w-3 h-3" /> Sampel Riset
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center font-mono">
                            {{ $jawaban->skor_ai ?? '-' }} / {{ $soal->skor_maks }}
                        </td>
                        <td class="px-6 py-4 text-center font-mono font-bold text-gray-900 bg-gray-50">
                            {{ $jawaban->skor_final ?? '-' }} / {{ $soal->skor_maks }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="bukaReview({{ $jawaban->id }}, '{{ $jawaban->jenis }}')" class="inline-flex items-center gap-1 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg text-xs font-medium shadow-sm transition-colors">
                                <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                                Review
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <x-heroicon-o-check-badge class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                            <p class="font-medium">Tidak ada jawaban uraian yang membutuhkan review guru saat ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Review -->
    @if($modalTerbuka && $jawabanAktif)
        @php
            $isQuiz = $tipeAktif === 'quiz';
            $murid = $isQuiz ? $jawabanAktif->hasilQuiz->murid : $jawabanAktif->murid;
            $soal = $isQuiz ? $jawabanAktif->soalQuiz : $jawabanAktif->soalPraktikum;
        @endphp
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="tutupModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white">Review & Validasi Penilaian</h3>
                        <button wire:click="tutupModal" class="text-indigo-200 hover:text-white">
                            <x-heroicon-o-x-mark class="w-6 h-6" />
                        </button>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Kolom Kiri: Soal & Jawaban Murid -->
                            <div class="space-y-6">
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pertanyaan</h4>
                                    <div class="prose prose-sm text-gray-800">
                                        {!! Str::markdown($soal->pertanyaan) !!}
                                    </div>
                                </div>
                                
                                <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                                    <h4 class="text-xs font-bold text-blue-800 uppercase tracking-wider mb-2">Jawaban {{ $murid->user->name }}</h4>
                                    <p class="text-sm font-mono text-gray-800 whitespace-pre-wrap">{{ $jawabanAktif->jawaban ?? '(Tidak menjawab)' }}</p>
                                </div>

                                <div>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Rubrik Acuan AI</h4>
                                    <ul class="space-y-2">
                                        @foreach($soal->rubrik ?? [] as $idx => $rubrik)
                                            <li class="bg-white border border-gray-200 rounded-lg p-3 flex justify-between text-sm">
                                                <span class="text-gray-700">{{ $rubrik['aspek'] }}</span>
                                                <span class="font-bold text-gray-900 font-mono">{{ $rubrik['skor'] }} Pts</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Kolom Kanan: Hasil AI & Override Form -->
                            <div class="space-y-6">
                                <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 relative">
                                    <div class="absolute top-4 right-4 bg-white border border-indigo-200 text-indigo-700 font-bold px-3 py-1 rounded-lg shadow-sm text-sm">
                                        AI: {{ $jawabanAktif->skor_ai ?? 0 }} / {{ $soal->skor_maks }}
                                    </div>
                                    <h4 class="text-xs font-bold text-indigo-800 uppercase tracking-wider mb-3">Analisis AI</h4>
                                    
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-700 italic">"{{ $jawabanAktif->feedback_ai ?? 'Tidak ada analisis dari AI.' }}"</p>
                                    </div>

                                    @if($jawabanAktif->detail_skor_ai)
                                        <div class="space-y-1 mt-3">
                                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Rincian AI</p>
                                            @foreach($jawabanAktif->detail_skor_ai as $kriteria => $s)
                                                <div class="flex justify-between text-xs py-1 border-b border-indigo-100 last:border-0">
                                                    <span class="text-gray-600">{{ $kriteria }}</span>
                                                    <span class="font-mono font-bold">{{ $s }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="bg-white border-2 border-emerald-200 rounded-xl p-5 shadow-sm">
                                    <h4 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                                        <x-heroicon-s-pencil-square class="w-5 h-5 text-emerald-600" />
                                        Validasi & Koreksi Akhir (Guru)
                                    </h4>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Skor Final</label>
                                            <div class="flex items-center gap-3">
                                                <input type="number" wire:model="skorFinal" min="0" max="{{ $soal->skor_maks }}" class="w-24 border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-lg font-bold font-mono">
                                                <span class="text-gray-500">/ {{ $soal->skor_maks }}</span>
                                            </div>
                                            @error('skorFinal') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Feedback Final untuk Murid</label>
                                            <textarea wire:model="feedbackFinal" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm"></textarea>
                                            @error('feedbackFinal') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 rounded-b-2xl">
                        <button wire:click="simpanValidasi(true)" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium shadow-sm hover:bg-indigo-700">
                            Timpa (Override)
                        </button>
                        <button wire:click="simpanValidasi(false)" class="bg-emerald-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium shadow-sm hover:bg-emerald-700" title="Simpan sebagai 'Divalidasi Guru' dengan menerima saran skor">
                            Setujui Skor Ini
                        </button>
                        <button wire:click="tutupModal" class="bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-medium shadow-sm hover:bg-gray-50">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
