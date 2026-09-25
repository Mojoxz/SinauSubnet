<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <x-heroicon-o-trophy class="w-8 h-8 text-amber-500" />
            Papan Peringkat Kelas
        </h1>
        <p class="text-gray-500 text-sm mt-2">Dapatkan poin dari Quiz, evaluasi Praktikum, dan Speed Bonus untuk meraih peringkat teratas!</p>
    </div>

    <!-- Info Peringkat Pribadi -->
    <div class="bg-indigo-900 rounded-2xl shadow-lg border border-indigo-700 overflow-hidden mb-10 relative">
        <div class="absolute right-0 top-0 w-64 h-64 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 transform translate-x-1/3 -translate-y-1/2"></div>
        <div class="relative z-10 px-8 py-6 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <div class="bg-amber-400 text-amber-900 w-16 h-16 rounded-full flex items-center justify-center text-2xl font-black shadow-inner border-4 border-amber-300">
                    #{{ $peringkatSaya }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white mb-1">Peringkat Anda Saat Ini</h2>
                    <p class="text-indigo-200 text-sm">Terus tingkatkan poin Anda untuk mengalahkan pesaing!</p>
                </div>
            </div>
            <div class="text-right hidden sm:block">
                <div class="text-3xl font-black text-amber-400 font-mono">{{ number_format($muridSaya->total_poin, 0, ',', '.') }}</div>
                <div class="text-xs text-indigo-300 font-medium uppercase tracking-widest mt-1">Total Poin</div>
            </div>
        </div>
    </div>

    <!-- Tabel Leaderboard -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-4 w-20 text-center">Peringkat</th>
                        <th class="px-6 py-4">Nama Murid</th>
                        <th class="px-6 py-4">Kelas</th>
                        <th class="px-6 py-4 text-right">Total Poin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @foreach($daftarPeringkat as $index => $muridRow)
                        @php
                            // Kalkulasi peringkat asli dengan mempertimbangkan current page halaman paginate
                            $rank = $daftarPeringkat->firstItem() + $index;
                            $isMe = $muridRow->id === $muridSaya->id;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors {{ $isMe ? 'bg-indigo-50/50 hover:bg-indigo-50' : '' }}">
                            <td class="px-6 py-4 text-center">
                                @if($rank === 1)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-700 font-bold border border-yellow-300 shadow-sm">1</span>
                                @elseif($rank === 2)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-700 font-bold border border-gray-300 shadow-sm">2</span>
                                @elseif($rank === 3)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-800 font-bold border border-orange-300 shadow-sm">3</span>
                                @else
                                    <span class="font-medium text-gray-500">{{ $rank }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold {{ $isMe ? 'text-indigo-700' : 'text-gray-900' }} flex items-center gap-2">
                                    {{ $muridRow->user->name }}
                                    @if($isMe) <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wider">Anda</span> @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $muridRow->kelas }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-mono font-bold text-gray-900 bg-gray-100 px-3 py-1 rounded-lg">{{ number_format($muridRow->total_poin, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($daftarPeringkat->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $daftarPeringkat->links() }}
            </div>
        @endif
    </div>
</div>
