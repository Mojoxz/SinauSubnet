<div>
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('guru.dashboard') }}" wire:navigate class="text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-2 font-medium text-sm">
            <x-heroicon-o-arrow-left class="w-4 h-4" />
            Kembali
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Penilaian Kinerja Praktik (Psikomotorik)</h1>
        <p class="text-gray-500 text-sm mt-1">Siswa: <span class="font-bold text-gray-700">{{ $murid->user->name }} ({{ $murid->kelas }})</span> &bull; Praktikum: <span class="font-bold text-gray-700">{{ $praktikum->judul }}</span></p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-sm flex items-center gap-3">
            <x-heroicon-o-check-circle class="w-5 h-5" />
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Kolom Kiri: Lampiran Praktikum & Instruksi -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-indigo-50 rounded-xl p-6 border border-indigo-200">
                <h3 class="font-bold text-indigo-900 flex items-center gap-2 mb-4">
                    <x-heroicon-o-document-magnifying-glass class="w-5 h-5" />
                    Berkas Bukti Praktik
                </h3>
                
                @if(count($jawabanMuridList) === 0)
                    <p class="text-sm text-indigo-700 italic">Siswa belum/tidak mengunggah berkas bukti untuk praktikum ini.</p>
                @else
                    <ul class="space-y-3">
                        @foreach($jawabanMuridList as $jawaban)
                            <li class="bg-white rounded-lg p-3 border border-indigo-100 shadow-sm flex flex-col gap-2">
                                <span class="text-xs font-bold text-gray-500 uppercase">Jawaban Soal {{ $jawaban->soalPraktikum->urutan }}</span>
                                <a href="{{ route('storage.bukti', $jawaban->id) }}" target="_blank" class="flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <x-heroicon-s-arrow-down-tray class="w-4 h-4" />
                                    Unduh Berkas
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-3">Panduan Penilaian</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <span class="font-bold text-emerald-600">4</span>
                        <span>Sangat Baik (Tepat, mandiri, sesuai prosedur)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold text-blue-600">3</span>
                        <span>Baik (Tepat, sedikit bantuan)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold text-amber-600">2</span>
                        <span>Cukup (Banyak bantuan, ada kesalahan kecil)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-bold text-rose-600">1</span>
                        <span>Kurang (Tidak mampu/salah total)</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Kolom Kanan: Form Rubrik -->
        <div class="lg:col-span-2">
            <form wire:submit.prevent="simpan" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-gray-500" />
                        Form Rubrik Psikomotorik
                    </h3>
                </div>
                
                <div class="p-6 space-y-8">
                    @foreach($rubrikList as $rubrik)
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h4 class="font-bold text-gray-900">{{ $rubrik->aspek }}</h4>
                                <p class="text-sm text-gray-500 mt-1">{{ $rubrik->indikator }}</p>
                            </div>
                            
                            <div class="flex gap-2 shrink-0">
                                @for($i = 1; $i <= 4; $i++)
                                    <label class="cursor-pointer relative">
                                        <input type="radio" wire:model="skorAspek.{{ $rubrik->id }}" value="{{ $i }}" class="peer sr-only">
                                        <div class="w-12 h-10 flex items-center justify-center rounded-lg border-2 border-gray-200 bg-white font-bold text-gray-500 hover:bg-gray-50 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 peer-checked:text-white transition-all">
                                            {{ $i }}
                                        </div>
                                    </label>
                                @endfor
                            </div>
                        </div>
                    @endforeach

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Guru (Opsional)</label>
                        <textarea wire:model="catatan" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Berikan evaluasi naratif mengenai kinerja praktik siswa ini..."></textarea>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-bold shadow-sm hover:bg-indigo-700 flex items-center gap-2">
                        <x-heroicon-s-check-circle class="w-5 h-5" />
                        Simpan Penilaian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
