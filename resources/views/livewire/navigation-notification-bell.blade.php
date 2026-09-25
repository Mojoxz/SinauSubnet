{{-- Komponen Lonceng Notifikasi di Topbar --}}
<div class="relative">
    <button class="relative rounded-full p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white">
        <x-heroicon-o-bell class="h-6 w-6" />
        
        @if ($unreadCount > 0)
            <span class="absolute right-1.5 top-1.5 flex h-3 w-3 items-center justify-center rounded-full bg-red-500 ring-2 ring-white dark:ring-gray-900">
                {{-- Indikator merah (tidak ada teks jika terlalu kecil, atau bisa render $unreadCount jika mau) --}}
            </span>
        @endif
    </button>
</div>
