<div>
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('guru.praktikum.index') }}" wire:navigate class="text-gray-500 hover:text-gray-900 transition-colors">
                <x-heroicon-o-arrow-left class="w-6 h-6" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $praktikum->judul }}</h1>
                <p class="text-gray-500 text-sm mt-1">Skenario Level {{ $praktikum->materi->level ?? '?' }} &bull; {{ $soalList->count() }} Soal Terdaftar</p>
            </div>
        </div>
        
        @if(!$modeForm)
        <button wire:click="toggleForm" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
            <x-heroicon-o-plus class="w-5 h-5" />
            Tambah Soal
        </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 flex items-start gap-3">
            <x-heroicon-o-check-circle class="w-5 h-5 flex-shrink-0 mt-0.5" />
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif
    
    @if (session()->has('warning'))
        <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg mb-6 flex items-start gap-3">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5 flex-shrink-0 mt-0.5" />
            <p class="text-sm font-medium">{{ session('warning') }}</p>
        </div>
    @endif

    @if($modeForm)
        <!-- Form Tambah/Edit Soal -->
        <div class="bg-white rounded-xl shadow-sm border border-indigo-200 overflow-hidden mb-8">
            <div class="bg-indigo-50/50 px-6 py-4 border-b border-indigo-100 flex justify-between items-center">
                <h2 class="font-bold text-indigo-900">{{ $soal_id ? 'Edit Soal' : 'Soal Praktikum Baru' }}</h2>
                <button wire:click="resetForm" class="text-gray-400 hover:text-gray-600">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>
            
            <form wire:submit.prevent="simpanSoal" class="p-6 space-y-6">
                <!-- Row 1 -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Soal</label>
                        <select wire:model.live="tipe" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="isian_singkat">Isian Singkat (Auto Koreksi)</option>
                            <option value="uraian">Uraian (AI + Manual Koreksi)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Level Taksonomi Bloom</label>
                        <select wire:model="level_bloom" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="C3">C3 - Mengaplikasikan</option>
                            <option value="C4">C4 - Menganalisis</option>
                            <option value="C5">C5 - Mengevaluasi</option>
                            <option value="C6">C6 - Mencipta</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Skor Maksimal</label>
                        <input type="number" wire:model="skor_maks" min="1" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        @error('skor_maks') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                        <input type="number" wire:model="urutan" min="1" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>
                </div>

                <!-- Pertanyaan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teks Pertanyaan (Markdown)</label>
                    <textarea wire:model="pertanyaan" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm"></textarea>
                    @error('pertanyaan') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Kunci Jawaban (Khusus Isian Singkat) -->
                @if($tipe === 'isian_singkat')
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <label class="block text-sm font-medium text-gray-900 mb-2">Kunci Jawaban Valid (Auto-Koreksi)</label>
                    <div class="flex gap-2 mb-3">
                        <input type="text" wire:model="tempKunci" wire:keydown.enter.prevent="addKunci" placeholder="Ketik jawaban, tekan Enter/Tambah" class="flex-1 border-gray-300 rounded-lg shadow-sm text-sm">
                        <button type="button" wire:click="addKunci" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm">Tambah</button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($kunci_jawaban as $index => $kunci)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-white border border-gray-300">
                                {{ $kunci }}
                                <button type="button" wire:click="removeKunci({{ $index }})" class="text-gray-400 hover:text-red-500">
                                    <x-heroicon-o-x-mark class="w-3 h-3" />
                                </button>
                            </span>
                        @endforeach
                    </div>
                    @if(empty($kunci_jawaban))
                        <p class="text-xs text-gray-500 mt-2">Belum ada kunci jawaban. Wajib ditambahkan agar sistem bisa menilai otomatis.</p>
                    @endif
                </div>
                @endif

                <!-- Rubrik Penilaian (Khusus Uraian) -->
                @if($tipe === 'uraian')
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-bold text-amber-900">Rubrik Penilaian AI/Manual</label>
                        <span class="text-xs font-medium px-2 py-1 bg-amber-200 text-amber-800 rounded">Total Skor Rubrik = {{ count($rubrik) > 0 ? array_sum(array_column($rubrik, 'skor')) : 0 }} / {{ $skor_maks }}</span>
                    </div>
                    <div class="flex gap-2 mb-3">
                        <input type="text" wire:model="tempRubrikAspek" placeholder="Kriteria Penilaian..." class="flex-1 border-gray-300 rounded-lg shadow-sm text-sm">
                        <input type="number" wire:model="tempRubrikSkor" placeholder="Skor" class="w-24 border-gray-300 rounded-lg shadow-sm text-sm">
                        <button type="button" wire:click="addRubrik" class="bg-amber-600 text-white px-4 py-2 rounded-lg text-sm">Tambah</button>
                    </div>
                    <ul class="space-y-2">
                        @foreach($rubrik as $index => $item)
                            <li class="flex justify-between items-center bg-white border border-amber-100 p-2 rounded text-sm">
                                <span class="text-gray-700">{{ $item['aspek'] }}</span>
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-amber-600">+{{ $item['skor'] }}</span>
                                    <button type="button" wire:click="removeRubrik({{ $index }})" class="text-gray-400 hover:text-red-500"><x-heroicon-o-trash class="w-4 h-4" /></button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <p class="text-xs text-amber-700 mt-2 font-medium">INFO PENTING: Total skor di rubrik wajib persis sama dengan Skor Maksimal soal ({{ $skor_maks }}).</p>
                </div>
                @endif

                <!-- Hint Bertahap -->
                <div class="p-4 border border-gray-200 rounded-lg">
                    <label class="block text-sm font-medium text-gray-900 mb-2">Bantuan Terpandu (Hint Bertahap)</label>
                    <div class="flex gap-2 mb-3">
                        <input type="text" wire:model="tempHint" wire:keydown.enter.prevent="addHint" placeholder="Ketik hint..." class="flex-1 border-gray-300 rounded-lg shadow-sm text-sm">
                        <button type="button" wire:click="addHint" class="bg-gray-100 text-gray-700 border border-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">Tambah</button>
                    </div>
                    <ul class="space-y-2">
                        @foreach($hint as $index => $h)
                            <li class="flex items-center gap-3 bg-gray-50 border border-gray-100 p-2 rounded text-sm">
                                <span class="bg-indigo-100 text-indigo-700 font-bold px-2 py-0.5 rounded text-xs">Hint {{ $index + 1 }}</span>
                                <span class="flex-1 text-gray-700">{{ $h }}</span>
                                <button type="button" wire:click="removeHint({{ $index }})" class="text-gray-400 hover:text-red-500"><x-heroicon-o-trash class="w-4 h-4" /></button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" wire:click="resetForm" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg text-sm font-medium transition-colors">Batal</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium shadow-sm transition-colors">
                        Simpan Soal
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Daftar Soal -->
    <div class="space-y-4">
        @forelse($soalList as $soal)
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm relative {{ !$soal->is_aktif ? 'opacity-60 bg-gray-50' : '' }}">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex gap-3 items-center">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-sm">
                            {{ $soal->urutan }}
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold uppercase tracking-wider {{ $soal->tipe->value === 'uraian' ? 'text-amber-600' : 'text-emerald-600' }}">
                                    {{ str_replace('_', ' ', $soal->tipe->value) }}
                                </span>
                                <span class="text-gray-300">&bull;</span>
                                <span class="text-xs font-medium text-gray-500">{{ $soal->level_bloom->value }}</span>
                                <span class="text-gray-300">&bull;</span>
                                <span class="text-xs font-medium text-gray-500">{{ $soal->skor_maks }} Poin</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($soal->is_aktif)
                            <button wire:click="editSoal({{ $soal->id }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Soal">
                                <x-heroicon-o-pencil-square class="w-5 h-5" />
                            </button>
                        @endif
                        <button wire:click="hapusSoal({{ $soal->id }})" wire:confirm="Yakin ingin menghapus soal ini?" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Soal">
                            <x-heroicon-o-trash class="w-5 h-5" />
                        </button>
                    </div>
                </div>
                
                <div class="pl-11 pr-4 prose prose-sm max-w-none prose-p:my-1 prose-pre:bg-gray-800 prose-pre:text-gray-100">
                    {!! Str::markdown($soal->pertanyaan) !!}
                </div>
                
                @if($soal->tipe->value === 'uraian' && is_array($soal->rubrik))
                    <div class="pl-11 mt-3">
                        <div class="inline-block bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 text-xs">
                            <span class="font-bold text-amber-800 block mb-1">Rubrik Validasi:</span>
                            <ul class="list-disc list-inside text-amber-700 space-y-0.5">
                                @foreach($soal->rubrik as $rb)
                                    <li>{{ $rb['aspek'] }} ({{ $rb['skor'] }} poin)</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                
                @if(is_array($soal->hint) && count($soal->hint) > 0)
                    <div class="pl-11 mt-3 flex gap-2">
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-1 rounded">
                            <x-heroicon-o-light-bulb class="w-3 h-3" /> {{ count($soal->hint) }} Hint
                        </span>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12 bg-white border border-dashed border-gray-300 rounded-xl">
                <x-heroicon-o-document-text class="w-12 h-12 mx-auto text-gray-300 mb-3" />
                <p class="font-medium text-gray-900 mb-1">Belum ada soal pada praktikum ini</p>
                <p class="text-sm text-gray-500 mb-4">Buat soal studi kasus pertama Anda.</p>
                <button wire:click="toggleForm" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm transition-colors">
                    + Tambah Soal
                </button>
            </div>
        @endforelse
    </div>
</div>
