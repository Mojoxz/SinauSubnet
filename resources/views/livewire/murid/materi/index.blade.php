<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Modul Pembelajaran Subnetting</h1>
        <p class="text-gray-600">Pelajari konsep subnetting secara bertahap. Selesaikan materi untuk membuka modul praktik.</p>
    </div>

    <div class="space-y-8">
        @foreach([1, 2, 3, 4] as $level)
            @php
                $materis = $materiPerLevel->get($level, collect());
                $isTerkunci = $level > $levelTertinggiMurid;
            @endphp
            
            <div class="bg-white rounded-2xl shadow-sm border {{ $isTerkunci ? 'border-gray-200' : 'border-indigo-100' }} overflow-hidden">
                <div class="{{ $isTerkunci ? 'bg-gray-50' : 'bg-indigo-50/50' }} px-6 py-4 border-b {{ $isTerkunci ? 'border-gray-200' : 'border-indigo-100' }} flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold {{ $isTerkunci ? 'text-gray-500' : 'text-indigo-900' }}">Level {{ $level }}</h2>
                        <p class="text-sm {{ $isTerkunci ? 'text-gray-400' : 'text-indigo-700/80' }}">
                            {{ ['Konsep Dasar Subnetting', 'Analisis Kebutuhan Subnet', 'Desain VLSM', 'Evaluasi & Troubleshooting'][$level-1] }}
                        </p>
                    </div>
                    @if($isTerkunci)
                        <div class="bg-white/80 p-2 rounded-full text-gray-400 border border-gray-200">
                            <x-heroicon-s-lock-closed class="w-5 h-5" />
                        </div>
                    @endif
                </div>
                
                <div class="p-6">
                    @if($materis->isEmpty())
                        <p class="text-gray-500 text-sm italic">Belum ada materi untuk level ini.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($materis as $materi)
                                @if($isTerkunci)
                                    <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/50 opacity-70">
                                        <div class="text-xs font-semibold text-gray-400 mb-2">MATERI {{ $materi->urutan }}</div>
                                        <h3 class="font-medium text-gray-600 leading-snug">{{ $materi->judul }}</h3>
                                    </div>
                                @else
                                    <a href="{{ route('murid.materi.baca', $materi->id) }}" wire:navigate class="group block border border-gray-200 hover:border-indigo-300 rounded-xl p-4 hover:shadow-md transition-all bg-white">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="text-xs font-bold text-indigo-600 tracking-wider">MATERI {{ $materi->urutan }}</div>
                                            <x-heroicon-o-book-open class="w-5 h-5 text-gray-400 group-hover:text-indigo-500 transition-colors" />
                                        </div>
                                        <h3 class="font-bold text-gray-900 leading-snug mb-2 group-hover:text-indigo-700 transition-colors">{{ $materi->judul }}</h3>
                                        <div class="flex items-center text-xs text-indigo-600 font-medium">
                                            Mulai Belajar
                                            <x-heroicon-s-arrow-right class="w-3 h-3 ml-1 group-hover:translate-x-1 transition-transform" />
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
