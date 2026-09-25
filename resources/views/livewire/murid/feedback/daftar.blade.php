<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <x-heroicon-o-chat-bubble-left-ellipsis class="w-8 h-8 text-blue-600" />
            Umpan Balik (Feedback) Guru
        </h1>
        <p class="text-gray-500 text-sm mt-2">Masukan, saran, dan evaluasi langsung dari guru untuk perkembangan belajar Anda.</p>
    </div>

    <div class="space-y-4">
        @forelse($feedbacks as $item)
            <div x-data="{ open: {{ $item->dibaca_pada ? 'false' : 'true' }} }" class="bg-white rounded-xl shadow-sm border {{ $item->dibaca_pada ? 'border-gray-200' : 'border-blue-300 ring-1 ring-blue-300' }} overflow-hidden transition-all">
                <div @click="open = !open; @if(!$item->dibaca_pada) $wire.tandaiDibaca({{ $item->id }}) @endif" class="px-6 py-4 cursor-pointer flex justify-between items-center {{ $item->dibaca_pada ? 'bg-gray-50 hover:bg-gray-100' : 'bg-blue-50 hover:bg-blue-100' }}">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg {{ $item->dibaca_pada ? 'bg-gray-200 text-gray-600' : 'bg-blue-200 text-blue-700' }}">
                            {{ substr($item->guru->user->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-bold {{ $item->dibaca_pada ? 'text-gray-800' : 'text-blue-900' }}">Dari: {{ $item->guru->user->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $item->created_at->translatedFormat('l, d M Y - H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if(!$item->dibaca_pada)
                            <span class="bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider animate-pulse">Baru</span>
                        @endif
                        <x-heroicon-o-chevron-down class="w-5 h-5 text-gray-400 transform transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" />
                    </div>
                </div>
                
                <div x-show="open" x-collapse x-cloak>
                    <div class="p-6 border-t {{ $item->dibaca_pada ? 'border-gray-100' : 'border-blue-100 bg-white' }}">
                        <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">{{ $item->isi }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-12 text-center">
                <x-heroicon-o-inbox class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                <h3 class="text-lg font-bold text-gray-700 mb-1">Tidak Ada Umpan Balik</h3>
                <p class="text-gray-500 text-sm">Belum ada pesan tertulis dari guru untuk Anda saat ini.</p>
            </div>
        @endforelse
    </div>

    @if($feedbacks->hasPages())
        <div class="mt-8">
            {{ $feedbacks->links() }}
        </div>
    @endif
</div>
