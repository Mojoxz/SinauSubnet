<div>
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('guru.quiz.index') }}" wire:navigate class="text-gray-500 hover:text-gray-900 transition-colors">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Buat Quiz Baru</h1>
            <p class="text-gray-500 text-sm mt-1">Tambahkan kuis evaluasi dengan batas waktu.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form wire:submit.prevent="simpan" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Materi Induk -->
                <div>
                    <label for="materi_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Materi Terkait</label>
                    <select id="materi_id" wire:model="materi_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @forelse($daftarMateri as $materi)
                            <option value="{{ $materi->id }}">Level {{ $materi->level }} - {{ $materi->judul }} {{ !$materi->is_aktif ? '(Diarsipkan)' : '' }}</option>
                        @empty
                            <option value="">-- Belum ada materi --</option>
                        @endforelse
                    </select>
                    @error('materi_id') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Judul -->
                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul Quiz</label>
                    <input type="text" id="judul" wire:model="judul" placeholder="Contoh: Kuis Evaluasi Subnetting Kelas C" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('judul') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <!-- Durasi & KKM & Bonus -->
                <div>
                    <label for="durasi_menit" class="block text-sm font-medium text-gray-700 mb-1">Durasi Ujian (Menit)</label>
                    <input type="number" id="durasi_menit" wire:model="durasi_menit" min="5" max="180" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('durasi_menit') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="kkm" class="block text-sm font-medium text-gray-700 mb-1">Batas Lulus (KKM)</label>
                    <input type="number" id="kkm" wire:model="kkm" min="1" max="100" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('kkm') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="bonus_kecepatan_maks" class="block text-sm font-medium text-gray-700 mb-1">Maksimal Bonus Kecepatan (Poin Gamifikasi)</label>
                    <input type="number" id="bonus_kecepatan_maks" wire:model="bonus_kecepatan_maks" min="0" max="1000" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Poin ekstra yang diberikan kepada murid jika berhasil menyelesaikan kuis lebih cepat dengan jawaban yang benar.</p>
                    @error('bonus_kecepatan_maks') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Deskripsi Markdown -->
            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi & Instruksi</label>
                <textarea id="deskripsi" wire:model="deskripsi" rows="4" placeholder="Tuliskan petunjuk pengerjaan (bisa markdown)..." class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm"></textarea>
                @error('deskripsi') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <!-- Is Aktif -->
            <div class="flex items-center">
                <input id="is_aktif" type="checkbox" wire:model="is_aktif" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="is_aktif" class="ml-2 block text-sm text-gray-900">
                    Quiz Aktif (Terbuka untuk dikerjakan murid)
                </label>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('guru.quiz.index') }}" wire:navigate class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Batal</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <x-heroicon-o-check class="w-5 h-5" />
                    Simpan & Kelola Soal
                </button>
            </div>
        </form>
    </div>
</div>
