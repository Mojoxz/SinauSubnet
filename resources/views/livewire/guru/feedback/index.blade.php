<div>
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Umpan Balik (Feedback) Murid</h1>
            <p class="text-gray-500 text-sm mt-1">Berikan masukan tertulis khusus untuk murid tertentu terkait performa belajarnya.</p>
        </div>
        <div>
            @if(!$modeForm)
                <button wire:click="bukaForm" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                    <x-heroicon-o-plus class="w-5 h-5" />
                    Kirim Feedback Baru
                </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-sm flex items-center gap-3">
            <x-heroicon-o-check-circle class="w-5 h-5" />
            {{ session('success') }}
        </div>
    @endif

    @if($modeForm)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h3 class="font-bold text-gray-800 mb-4">{{ $feedback_id ? 'Ubah Feedback' : 'Kirim Feedback Baru' }}</h3>
            
            <form wire:submit.prevent="simpan" class="space-y-4 max-w-2xl">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Murid</label>
                    <select wire:model="murid_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Murid --</option>
                        @foreach($daftarMurid as $m)
                            <option value="{{ $m->id }}">{{ $m->user->name }} ({{ $m->kelas }})</option>
                        @endforeach
                    </select>
                    @error('murid_id') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Isi Feedback</label>
                    <textarea wire:model="isi" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Tuliskan masukan yang membangun..."></textarea>
                    @error('isi') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg font-medium shadow-sm hover:bg-indigo-700">
                        Simpan & Kirim
                    </button>
                    <button type="button" wire:click="resetForm" class="bg-white border border-gray-300 text-gray-700 px-5 py-2 rounded-lg font-medium shadow-sm hover:bg-gray-50">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-4">Tujuan (Murid)</th>
                    <th class="px-6 py-4 w-1/2">Isi Feedback</th>
                    <th class="px-6 py-4 text-center">Status Baca</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($feedbacks as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $item->murid->user->name }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $item->murid->kelas }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            {{ Str::limit($item->isi, 100) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->dibaca_pada)
                                <span class="inline-flex items-center gap-1 text-emerald-600 text-xs font-bold uppercase tracking-wider bg-emerald-50 px-2 py-1 rounded border border-emerald-200">
                                    <x-heroicon-o-check-circle class="w-4 h-4" /> Dibaca
                                </span>
                                <div class="text-[10px] text-gray-400 mt-1">{{ \Carbon\Carbon::parse($item->dibaca_pada)->format('d/m/Y H:i') }}</div>
                            @else
                                <span class="inline-flex items-center gap-1 text-gray-500 text-xs font-bold uppercase tracking-wider bg-gray-100 px-2 py-1 rounded border border-gray-200">
                                    <x-heroicon-o-clock class="w-4 h-4" /> Belum
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button wire:click="editFeedback({{ $item->id }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Ubah">
                                    <x-heroicon-o-pencil-square class="w-5 h-5" />
                                </button>
                                <button wire:click="hapusFeedback({{ $item->id }})" wire:confirm="Yakin ingin menghapus feedback ini?" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <x-heroicon-o-trash class="w-5 h-5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <x-heroicon-o-chat-bubble-left-right class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                            <p class="font-medium">Belum ada feedback yang dikirim ke murid.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($feedbacks->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $feedbacks->links() }}
            </div>
        @endif
    </div>
</div>
