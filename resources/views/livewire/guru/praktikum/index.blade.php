<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Praktikum PBL (Studi Kasus)</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola studi kasus dan instruksi praktikum untuk dihubungkan dengan materi.</p>
        </div>
        <a href="{{ route('guru.praktikum.buat') }}" wire:navigate class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
            <x-heroicon-o-plus class="w-5 h-5" />
            Praktikum Baru
        </a>
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
            <div>
                <p class="text-sm font-bold mb-1">Pengarsipan Otomatis (Soft-Archive)</p>
                <p class="text-sm">{{ session('warning') }}</p>
            </div>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    <th class="px-6 py-4">Materi Terkait (Level)</th>
                    <th class="px-6 py-4">Judul Praktikum</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm">
                @forelse($praktikums as $praktikum)
                    <tr class="hover:bg-gray-50 transition-colors {{ !$praktikum->is_aktif ? 'opacity-60 bg-gray-50' : '' }}">
                        <td class="px-6 py-4 text-gray-700">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800 mb-1">
                                Level {{ $praktikum->materi->level ?? '?' }}
                            </span>
                            <div class="font-medium text-xs truncate max-w-[200px]" title="{{ $praktikum->materi->judul ?? 'N/A' }}">
                                {{ $praktikum->materi->judul ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $praktikum->judul }}
                        </td>
                        <td class="px-6 py-4">
                            @if($praktikum->is_aktif)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> Diarsipkan
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-medium">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('guru.praktikum.detail', $praktikum->id) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors">Kelola Soal</a>
                                @if($praktikum->is_aktif)
                                    <a href="{{ route('guru.praktikum.ubah', $praktikum->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors">Edit Info</a>
                                @endif
                                <button wire:click="deletePraktikum({{ $praktikum->id }})" 
                                        wire:confirm="Yakin ingin menghapus praktikum ini?"
                                        class="text-rose-600 hover:text-rose-900 transition-colors">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <x-heroicon-o-beaker class="w-12 h-12 mx-auto text-gray-300 mb-3" />
                            <p class="font-medium text-gray-900 mb-1">Belum ada praktikum</p>
                            <p class="text-sm">Silakan buat modul praktikum studi kasus baru.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
