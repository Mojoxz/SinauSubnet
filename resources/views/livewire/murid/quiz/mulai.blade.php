<div>
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('murid.dashboard') }}" wire:navigate class="text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-2 font-medium text-sm">
            <x-heroicon-o-arrow-left class="w-4 h-4" />
            Kembali ke Dashboard
        </a>
    </div>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden text-center p-12">
            <div class="w-20 h-20 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <x-heroicon-o-document-check class="w-10 h-10" />
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $quiz->judul }}</h1>
            <span class="inline-block bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded-full text-xs uppercase tracking-wider mb-6">
                Evaluasi Level {{ $quiz->materi->level ?? '?' }}
            </span>

            <div class="prose prose-indigo max-w-none text-gray-600 mb-8 mx-auto">
                {!! Str::markdown($quiz->deskripsi ?? 'Silakan kerjakan kuis ini dengan jujur dan sungguh-sungguh.') !!}
            </div>

            <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto mb-10">
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <x-heroicon-o-clock class="w-6 h-6 text-gray-400 mx-auto mb-2" />
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Durasi</p>
                    <p class="text-xl font-bold text-gray-900">{{ $quiz->durasi_menit }} Menit</p>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <x-heroicon-o-check-badge class="w-6 h-6 text-gray-400 mx-auto mb-2" />
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Batas Lulus (KKM)</p>
                    <p class="text-xl font-bold text-emerald-600">{{ $quiz->kkm }}</p>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-4 text-sm text-left mb-8 flex items-start gap-3">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6 shrink-0 mt-0.5" />
                <div>
                    <strong>Peraturan & Peringatan Ujian:</strong>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Waktu ujian akan terus berjalan secara otomatis dari server sejak tombol ditekan.</li>
                        <li>Jangan keluar/menutup browser. Waktu akan terus berjalan meski Anda logout.</li>
                        <li>Hanya ada <strong>1 (satu) kesempatan</strong> pengerjaan. Jika batas waktu habis, sistem akan menutup kuis secara otomatis.</li>
                    </ul>
                </div>
            </div>

            @if($hasilKuisAda)
                <button wire:click="mulaiUjian" wire:loading.attr="disabled" class="bg-amber-600 hover:bg-amber-700 text-white w-full sm:w-auto px-10 py-4 rounded-xl font-bold text-lg shadow-md hover:shadow-lg transition-all flex justify-center items-center gap-3 mx-auto group">
                    <span wire:loading.remove>Lanjutkan Pengerjaan Tertunda</span>
                    <span wire:loading>Memuat...</span>
                    <x-heroicon-s-arrow-right wire:loading.remove class="w-6 h-6 group-hover:translate-x-1 transition-transform" />
                </button>
            @else
                <button wire:click="mulaiUjian" wire:loading.attr="disabled" class="bg-indigo-600 hover:bg-indigo-700 text-white w-full sm:w-auto px-10 py-4 rounded-xl font-bold text-lg shadow-md hover:shadow-lg transition-all flex justify-center items-center gap-3 mx-auto group">
                    <span wire:loading.remove>Mulai Ujian Sekarang</span>
                    <span wire:loading>Memuat...</span>
                    <x-heroicon-s-arrow-right wire:loading.remove class="w-6 h-6 group-hover:translate-x-1 transition-transform" />
                </button>
            @endif
        </div>
    </div>
</div>
