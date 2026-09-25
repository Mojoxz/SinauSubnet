<div>
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('guru.praktikum.index') }}" wire:navigate class="text-gray-500 hover:text-gray-900 transition-colors">
            <x-heroicon-o-arrow-left class="w-6 h-6" />
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Ubah Praktikum</h1>
            <p class="text-gray-500 text-sm mt-1">Perbarui skenario studi kasus PBL ini.</p>
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
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul Praktikum</label>
                    <input type="text" id="judul" wire:model="judul" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('judul') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Konten Skenario Markdown -->
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label for="studi_kasus" class="block text-sm font-medium text-gray-700">Skenario Studi Kasus PBL</label>
                    <span class="text-xs text-indigo-600 font-medium bg-indigo-50 px-2 py-0.5 rounded">Markdown Supported</span>
                </div>
                <textarea id="studi_kasus" wire:model="studi_kasus" rows="12" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm leading-relaxed"></textarea>
                @error('studi_kasus') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <!-- Is Aktif -->
            <div class="flex items-center">
                <input id="is_aktif" type="checkbox" wire:model="is_aktif" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="is_aktif" class="ml-2 block text-sm text-gray-900">
                    Praktikum Aktif (Dapat dikerjakan oleh murid)
                </label>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('guru.praktikum.index') }}" wire:navigate class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Batal</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <x-heroicon-o-check class="w-5 h-5" />
                    Perbarui Praktikum
                </button>
            </div>
        </form>
    </div>
</div>
