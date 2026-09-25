<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <x-heroicon-o-document-chart-bar class="w-8 h-8 text-emerald-600" />
            Laporan & Ekspor Nilai
        </h1>
        <p class="text-gray-500 text-sm mt-2">Pusat monitoring hasil evaluasi AI dan guru. Gunakan filter untuk mengunduh rekapitulasi Excel atau rapot PDF individu.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Panel Filter -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-6">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <x-heroicon-o-funnel class="w-5 h-5 text-gray-400" />
                    Filter Query Laporan
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Laporan</label>
                        <select wire:model.live="tipeLaporan" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                            <option value="nilai_akhir">Rekap Nilai Akhir Kelas</option>
                            <option value="hasil_quiz">Rekap Hasil Kuis / Modul</option>
                            <option value="hasil_praktikum">Rekap Hasil Praktikum</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter Kelas</label>
                        <select wire:model.live="kelas" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                            <option value="">Semua Kelas</option>
                            @foreach($daftarKelas as $k)
                                <option value="{{ $k }}">{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($tipeLaporan === 'hasil_quiz' || $tipeLaporan === 'hasil_praktikum')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Modul Ujian</label>
                            <select wire:model.live="modulId" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                                <option value="">Seluruh Modul (Semua)</option>
                                @foreach($daftarModul as $modul)
                                    <option value="{{ $modul->id }}">
                                        {{ $modul->materi->judul ?? '' }} - {{ $modul->judul }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100">
                    <button wire:click="eksporExcel" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2">
                        <x-heroicon-s-arrow-down-tray class="w-5 h-5" />
                        Ekspor ke Excel
                    </button>
                    <p class="text-[10px] text-gray-400 text-center mt-2 leading-tight">File spreadsheet akan digenerate secara on-the-fly berdasarkan filter di atas.</p>
                </div>
            </div>
        </div>

        <!-- Tabel Preview Murid -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Preview Data Siswa</h3>
                    <span class="text-xs bg-indigo-100 text-indigo-800 font-bold px-2 py-1 rounded">{{ $murids->total() }} Data</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Nama Lengkap</th>
                                <th class="px-6 py-3 text-center">Kelas</th>
                                <th class="px-6 py-3 text-center">Gamifikasi</th>
                                <th class="px-6 py-3 text-right">Rapot Individu (PDF)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($murids as $murid)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-3 font-medium text-gray-900">
                                        {{ $murid->user->name }}
                                        <div class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $murid->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-3 text-center text-gray-600">
                                        {{ $murid->kelas }}
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 text-xs font-bold px-2 py-0.5 rounded border border-amber-200 font-mono">
                                            <x-heroicon-s-star class="w-3 h-3" /> {{ number_format($murid->total_poin, 0, ',', '.') }} Pts
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <button wire:click="eksporPdfIndividu({{ $murid->id }})" class="inline-flex items-center gap-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                                            <x-heroicon-s-document-arrow-down class="w-4 h-4" />
                                            Cetak PDF
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        <x-heroicon-o-users class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                                        <p class="font-medium">Tidak ada data murid yang sesuai filter.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($murids->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $murids->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
