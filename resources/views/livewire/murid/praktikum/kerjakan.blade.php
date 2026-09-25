<div>
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('murid.materi.index') }}" wire:navigate class="text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-2 font-medium text-sm">
            <x-heroicon-o-arrow-left class="w-4 h-4" />
            Kembali ke Modul
        </a>
    </div>

    <!-- Header & Skenario Praktikum -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="bg-indigo-900 text-white p-8 relative overflow-hidden">
            <div class="relative z-10">
                <span class="bg-indigo-700 text-indigo-100 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-3 inline-block">
                    Modul Praktikum &bull; Level {{ $praktikum->materi->level ?? '?' }}
                </span>
                <h1 class="text-3xl font-bold leading-tight mb-2">{{ $praktikum->judul }}</h1>
                <p class="text-indigo-200">Selesaikan seluruh studi kasus berdasarkan skenario yang diberikan di bawah ini.</p>
            </div>
            <div class="absolute right-0 top-0 w-64 h-64 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 transform translate-x-1/2 -translate-y-1/2"></div>
        </div>
        
        <div class="p-8 border-b border-gray-200 prose prose-indigo max-w-none">
            {!! Str::markdown($praktikum->studi_kasus) !!}
        </div>
        
        <!-- Alat Bantu Subnetting (Komponen Domain) -->
        <div class="bg-gray-50 p-6 border-b border-gray-200">
            <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                <x-heroicon-o-calculator class="w-5 h-5 text-indigo-600" />
                Alat Bantu Analisis IP (Scratchpad)
            </h3>
            <div class="flex flex-col md:flex-row gap-4 items-start">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Cek Representasi Biner IP:</label>
                    <input type="text" wire:model.live.debounce.500ms="scratchpad_ip" class="w-full max-w-xs border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm mb-3" placeholder="Misal: 192.168.1.1">
                    
                    @php
                        // Validasi sederhana sebelum render agar tidak error explode
                        $validIp = filter_var($scratchpad_ip ?? '192.168.1.0', FILTER_VALIDATE_IP);
                    @endphp
                    @if($validIp)
                        <x-domain.alamat-ip :ip="$validIp" />
                    @else
                        <span class="text-xs text-red-500 font-mono">Format IP tidak valid.</span>
                    @endif
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">Gunakan alat ini untuk memandu pemahaman Anda dalam menentukan Network, Host, dan Broadcast sebelum menjawab ke dalam form di bawah.</p>
        </div>
    </div>

    <!-- Daftar Soal -->
    <form wire:submit.prevent="submitPraktikum" class="space-y-8">
        @foreach($soals as $index => $soal)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" id="soal-{{ $soal->id }}">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="font-bold text-gray-800 flex items-center gap-2">
                        <span class="bg-indigo-600 text-white w-6 h-6 flex items-center justify-center rounded-full text-xs">{{ $index + 1 }}</span>
                        Pertanyaan {{ $soal->tipe->value === 'uraian' ? 'Uraian' : 'Isian' }}
                    </h2>
                    <span class="text-xs font-medium text-gray-500">{{ $soal->skor_maks }} Poin</span>
                </div>
                
                <div class="p-6">
                    <!-- Teks Pertanyaan -->
                    <div class="prose prose-sm max-w-none mb-6">
                        {!! Str::markdown($soal->pertanyaan) !!}
                    </div>

                    <!-- Area Jawaban -->
                    <div>
                        <div class="flex justify-between mb-1">
                            <label class="block text-sm font-bold text-gray-700">Jawaban Anda:</label>
                            @if($soal->tipe->value === 'uraian')
                                <span class="text-xs text-gray-400" x-data="{ count: 0 }" x-init="$watch('count', val => count = val)">
                                    <span x-text="$wire.jawaban[{{ $soal->id }}]?.length || 0"></span> / 1000 Karakter
                                </span>
                            @endif
                        </div>
                        
                        @if($soal->tipe->value === 'uraian')
                            <textarea wire:model="jawaban.{{ $soal->id }}" rows="5" maxlength="1000" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm" placeholder="Tuliskan analisis atau konfigurasi lengkap Anda..."></textarea>
                        @else
                            <input type="text" wire:model="jawaban.{{ $soal->id }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm" placeholder="Jawaban singkat Anda...">
                        @endif
                        
                        @error('jawaban.'.$soal->id) <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Hint Bertahap -->
                    @if(is_array($soal->hint) && count($soal->hint) > 0)
                        <div class="mt-6 border-t border-gray-100 pt-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-bold text-gray-700 flex items-center gap-1.5">
                                    <x-heroicon-o-light-bulb class="w-4 h-4 text-amber-500" />
                                    Bantuan Tersedia
                                </h4>
                                @if($hintTerbuka[$soal->id] < count($soal->hint) - 1)
                                    <button type="button" wire:click="bukaHint({{ $soal->id }}, {{ count($soal->hint) }})" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                        Tampilkan Hint Selanjutnya &rarr;
                                    </button>
                                @endif
                            </div>
                            
                            <div class="space-y-2">
                                @foreach($soal->hint as $h_idx => $h_text)
                                    @if($h_idx <= $hintTerbuka[$soal->id])
                                        <div class="bg-amber-50 border border-amber-100 p-3 rounded-lg text-sm text-amber-900 flex gap-3 animate-fade-in">
                                            <span class="font-bold shrink-0">Hint {{ $h_idx + 1 }}:</span>
                                            <span>{{ $h_text }}</span>
                                        </div>
                                    @else
                                        <div class="bg-gray-50 border border-dashed border-gray-300 p-3 rounded-lg text-sm text-gray-400 flex items-center gap-2 select-none">
                                            <x-heroicon-s-lock-closed class="w-4 h-4" /> Hint terkunci
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Private File Upload (Bukti Praktik) -->
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-8 shadow-sm">
            <h2 class="text-xl font-bold text-indigo-900 mb-2">Unggah Bukti Praktikum</h2>
            <p class="text-indigo-700 text-sm mb-6">Unggah file Cisco Packet Tracer (.pkt/.pka) atau *screenshot* topologi (JPG/PNG) sebagai bukti Anda telah mempraktikkan skenario di atas secara nyata.</p>
            
            <div class="bg-white p-6 rounded-xl border border-indigo-100 flex flex-col items-center justify-center text-center">
                <x-heroicon-o-cloud-arrow-up class="w-12 h-12 text-indigo-300 mb-3" />
                
                <input type="file" wire:model="file_bukti" id="file_bukti" accept=".pkt,.pka,.jpg,.jpeg,.png" class="block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-indigo-50 file:text-indigo-700
                    hover:file:bg-indigo-100
                    cursor-pointer focus:outline-none max-w-sm mx-auto
                "/>
                
                <p class="text-xs text-gray-400 mt-3">Maksimal 10MB. File akan dilindungi dan hanya dapat diakses oleh Anda dan Guru pembimbing Anda (Private Storage).</p>
                
                <div wire:loading wire:target="file_bukti" class="mt-4 text-sm font-medium text-indigo-600">
                    Mengunggah file ke server aman...
                </div>
                
                @error('file_bukti') <span class="text-sm text-red-600 mt-4 block font-bold">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4 pb-12">
            <button type="submit" wire:loading.attr="disabled" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-xl font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2 group">
                <span wire:loading.remove wire:target="submitPraktikum">Selesaikan & Kumpulkan Praktikum</span>
                <span wire:loading wire:target="submitPraktikum">Menyimpan & Menganalisis...</span>
                <x-heroicon-s-paper-airplane wire:loading.remove wire:target="submitPraktikum" class="w-5 h-5 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" />
            </button>
        </div>
    </form>
</div>
