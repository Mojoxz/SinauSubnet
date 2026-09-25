<div>
    <!-- Sticky Header Timer -->
    <div class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm mb-8" x-data="timerInit({{ $sisaDetik }})">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-gray-900 truncate max-w-xs md:max-w-md">{{ $quiz->judul }}</h1>
                <p class="text-sm text-gray-500">Evaluasi Level {{ $quiz->materi->level ?? '?' }}</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 px-4 py-2 rounded-lg" :class="{'bg-red-50 border-red-200 text-red-700': timeLeft <= 60}">
                    <x-heroicon-o-clock class="w-5 h-5 text-gray-500" :class="{'text-red-500 animate-pulse': timeLeft <= 60}" />
                    <span class="font-mono font-bold text-lg" x-text="formattedTime"></span>
                </div>
                <button wire:click="submitQuiz" wire:confirm="Anda yakin ingin mengakhiri kuis ini?" class="hidden md:flex bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm items-center gap-2">
                    <x-heroicon-s-paper-airplane class="w-4 h-4" />
                    Kumpulkan
                </button>
            </div>
        </div>
    </div>

    <!-- Daftar Soal -->
    <div class="max-w-4xl mx-auto space-y-6 pb-24">
        @foreach($soals as $index => $soal)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" id="soal-{{ $soal->id }}">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="font-bold text-gray-800 flex items-center gap-2">
                        <span class="bg-indigo-600 text-white w-6 h-6 flex items-center justify-center rounded-full text-xs">{{ $index + 1 }}</span>
                        Pertanyaan {{ str_replace('_', ' ', $soal->tipe->value) }}
                    </h2>
                    <span class="text-xs font-medium text-gray-500">{{ $soal->skor_maks }} Poin</span>
                </div>
                
                <div class="p-6 lg:p-8">
                    <!-- Teks Pertanyaan -->
                    <div class="prose prose-sm max-w-none mb-8 prose-pre:bg-gray-800 prose-pre:text-gray-100">
                        {!! Str::markdown($soal->pertanyaan) !!}
                    </div>

                    <!-- Area Input Jawaban -->
                    <div>
                        @if($soal->tipe->value === 'pilihan_ganda')
                            <div class="space-y-3">
                                @foreach($soal->opsi as $idx => $opsiText)
                                    <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition-colors {{ (isset($jawaban[$soal->id]) && $jawaban[$soal->id] === $opsiText) ? 'bg-indigo-50 border-indigo-300 ring-1 ring-indigo-300' : '' }}">
                                        <input type="radio" wire:model.live.debounce.300ms="jawaban.{{ $soal->id }}" value="{{ $opsiText }}" class="mt-0.5 w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700 font-medium leading-tight select-none">
                                            {{ chr(65 + $idx) }}. {{ $opsiText }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif($soal->tipe->value === 'isian_singkat')
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jawaban Singkat:</label>
                            <input type="text" wire:model.live.debounce.500ms="jawaban.{{ $soal->id }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm" placeholder="Ketik jawaban Anda...">
                        @elseif($soal->tipe->value === 'uraian')
                            <div class="flex justify-between mb-2">
                                <label class="block text-sm font-bold text-gray-700">Analisis/Uraian:</label>
                                <span class="text-xs text-gray-400 font-mono" x-data="{ count: 0 }" x-init="$watch('count', val => count = val)">
                                    <span x-text="$wire.jawaban[{{ $soal->id }}]?.length || 0"></span> / 1000 Karakter
                                </span>
                            </div>
                            <textarea wire:model.live.debounce.1000ms="jawaban.{{ $soal->id }}" rows="6" maxlength="1000" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm" placeholder="Jelaskan jawaban Anda secara detail..."></textarea>
                            <p class="text-xs text-gray-400 mt-2">Perubahan akan disimpan secara otomatis.</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        
        <!-- Mobile Submit Button -->
        <div class="md:hidden mt-8 text-center">
            <button wire:click="submitQuiz" wire:confirm="Anda yakin ingin mengakhiri kuis ini?" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-xl font-bold shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                <x-heroicon-s-paper-airplane class="w-5 h-5" />
                Selesaikan & Kumpulkan Ujian
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('timerInit', (initialSeconds) => ({
            timeLeft: initialSeconds,
            formattedTime: '00:00',
            
            init() {
                this.updateFormattedTime();
                
                const interval = setInterval(() => {
                    this.timeLeft--;
                    this.updateFormattedTime();
                    
                    if (this.timeLeft <= 0) {
                        clearInterval(interval);
                        // Trigger Livewire auto-submit
                        @this.call('prosesSubmit', true);
                    }
                }, 1000);
            },
            
            updateFormattedTime() {
                if(this.timeLeft <= 0) {
                    this.formattedTime = '00:00';
                    return;
                }
                const m = Math.floor(this.timeLeft / 60).toString().padStart(2, '0');
                const s = (this.timeLeft % 60).toString().padStart(2, '0');
                this.formattedTime = `${m}:${s}`;
            }
        }));
    });
</script>
@endpush
