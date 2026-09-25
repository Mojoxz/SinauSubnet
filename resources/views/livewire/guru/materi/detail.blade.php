<div>
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('guru.materi.index') }}" wire:navigate class="text-gray-500 hover:text-gray-900 transition-colors">
                <x-heroicon-o-arrow-left class="w-6 h-6" />
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $materi->judul }}</h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800">
                        Level {{ $materi->level }}
                    </span>
                </div>
                <p class="text-gray-500 text-sm mt-1">Urutan ke-{{ $materi->urutan }} &bull; {{ $materi->is_aktif ? 'Aktif' : 'Diarsipkan' }}</p>
            </div>
        </div>
        
        @if($materi->is_aktif)
        <a href="{{ route('guru.materi.ubah', $materi->id) }}" wire:navigate class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
            <x-heroicon-o-pencil-square class="w-4 h-4" />
            Edit Materi
        </a>
        @endif
    </div>

    <!-- Preview Box -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gray-50 px-6 py-3 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <x-heroicon-o-eye class="w-4 h-4" />
                Preview Tampilan Murid
            </h2>
        </div>
        
        <!-- Markdown Rendered Content -->
        <div class="p-8 lg:p-12 prose prose-indigo max-w-none prose-headings:font-bold prose-a:text-indigo-600 prose-img:rounded-xl">
            {!! $htmlKonten !!}
        </div>
    </div>
</div>
