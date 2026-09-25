<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <x-heroicon-o-check-badge class="w-8 h-8 text-emerald-500" />
            Koleksi Lencana
        </h1>
        <p class="text-gray-500 text-sm mt-2">Dapatkan lencana eksklusif dengan mengumpulkan poin gamifikasi dari kuis dan praktikum.</p>
    </div>

    <!-- Header Stats -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-10 flex flex-col sm:flex-row items-center justify-between gap-6">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Progres Pengumpulan Lencana</h2>
            <p class="text-sm text-gray-500 mt-1">Anda telah mengumpulkan <strong class="text-indigo-600">{{ $badgesDidapat->count() }}</strong> dari <strong class="text-gray-700">{{ $badgesDidapat->count() + $badgesTerkunci->count() }}</strong> lencana yang tersedia.</p>
        </div>
        <div class="bg-gray-50 rounded-xl px-6 py-4 text-center min-w-[150px]">
            <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Poin</span>
            <span class="text-2xl font-black text-emerald-600 font-mono">{{ number_format($totalPoin, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="space-y-12">
        <!-- Badges Obtained -->
        <section>
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                Lencana Anda
                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-0.5 rounded-full">{{ $badgesDidapat->count() }}</span>
            </h3>
            
            @if($badgesDidapat->isEmpty())
                <div class="bg-gray-50 border border-dashed border-gray-300 rounded-xl p-8 text-center">
                    <x-heroicon-o-shield-exclamation class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                    <p class="text-gray-600 font-medium">Belum ada lencana yang diperoleh.</p>
                    <p class="text-sm text-gray-500 mt-1">Selesaikan materi dan ujian untuk mulai mengumpulkan lencana!</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($badgesDidapat as $badge)
                        <div class="bg-gradient-to-br from-white to-emerald-50 rounded-2xl shadow-sm border border-emerald-100 p-6 flex flex-col items-center text-center transform transition-transform hover:-translate-y-1 hover:shadow-md">
                            <div class="w-20 h-20 mb-4 flex items-center justify-center bg-white rounded-full shadow-inner border-4 border-emerald-200">
                                <!-- Fallback ke icon generik jika icon tidak dikenali -->
                                <x-heroicon-s-check-badge class="w-10 h-10 text-emerald-500" />
                            </div>
                            <h4 class="font-bold text-gray-900 mb-1">{{ $badge->nama }}</h4>
                            <p class="text-xs text-gray-600 mb-4 flex-grow">{{ $badge->deskripsi }}</p>
                            <div class="w-full pt-3 border-t border-emerald-200/50 flex justify-between items-center text-xs">
                                <span class="font-medium text-emerald-700">{{ number_format($badge->syarat_poin, 0, ',', '.') }} Pts</span>
                                <span class="text-gray-400" title="{{ $badge->diperoleh_pada }}">
                                    {{ \Carbon\Carbon::parse($badge->diperoleh_pada)->translatedFormat('d M Y') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- Badges Locked -->
        <section>
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                Lencana Terkunci
                <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-0.5 rounded-full">{{ $badgesTerkunci->count() }}</span>
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($badgesTerkunci as $badge)
                    @php
                        $progress = min(100, ($totalPoin / max($badge->syarat_poin, 1)) * 100);
                    @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col items-center text-center grayscale opacity-70 hover:grayscale-0 hover:opacity-100 transition-all">
                        <div class="w-20 h-20 mb-4 flex items-center justify-center bg-gray-100 rounded-full border-4 border-gray-200">
                            <x-heroicon-s-lock-closed class="w-8 h-8 text-gray-400" />
                        </div>
                        <h4 class="font-bold text-gray-700 mb-1">{{ $badge->nama }}</h4>
                        <p class="text-xs text-gray-500 mb-4 flex-grow">{{ $badge->deskripsi }}</p>
                        
                        <div class="w-full">
                            <div class="flex justify-between text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">
                                <span>Progress</span>
                                <span>{{ number_format($totalPoin, 0, ',', '.') }} / {{ number_format($badge->syarat_poin, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gray-400 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</div>
