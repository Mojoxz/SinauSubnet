<div class="space-y-1">
    @if(auth()->user()->isGuru())
        <a href="{{ route('guru.dashboard') }}" wire:navigate class="{{ request()->routeIs('guru.dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
            <x-heroicon-o-home class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('guru.dashboard') ? 'text-indigo-600' : 'text-gray-400' }}" />
            Dashboard
        </a>
        <a href="{{ route('guru.materi.index') }}" wire:navigate class="{{ request()->routeIs('guru.materi.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
            <x-heroicon-o-book-open class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('guru.materi.*') ? 'text-indigo-600' : 'text-gray-400' }}" />
            Modul Materi
        </a>
        <a href="{{ route('guru.praktikum.index') }}" wire:navigate class="{{ request()->routeIs('guru.praktikum.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
            <x-heroicon-o-beaker class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('guru.praktikum.*') ? 'text-indigo-600' : 'text-gray-400' }}" />
            Modul Praktikum
        </a>
        <a href="{{ route('guru.quiz.index') }}" wire:navigate class="{{ request()->routeIs('guru.quiz.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
            <x-heroicon-o-document-text class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('guru.quiz.*') ? 'text-indigo-600' : 'text-gray-400' }}" />
            Modul Quiz
        </a>

        <div class="pt-4 mt-4 border-t border-gray-100">
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Penilaian & Evaluasi</p>
            <a href="{{ route('guru.penilaian.uraian') }}" wire:navigate class="{{ request()->routeIs('guru.penilaian.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                <x-heroicon-o-clipboard-document-check class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('guru.penilaian.*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                Koreksi Jawaban AI
            </a>
            <a href="{{ route('guru.feedback.index') }}" wire:navigate class="{{ request()->routeIs('guru.feedback.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                <x-heroicon-o-chat-bubble-left-ellipsis class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('guru.feedback.*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                Kirim Feedback
            </a>
            <a href="{{ route('guru.laporan.index') }}" wire:navigate class="{{ request()->routeIs('guru.laporan.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                <x-heroicon-o-document-chart-bar class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('guru.laporan.*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                Laporan & Ekspor
            </a>
        </div>
    @else
        <a href="{{ route('murid.dashboard') }}" wire:navigate class="{{ request()->routeIs('murid.dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
            <x-heroicon-o-home class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('murid.dashboard') ? 'text-indigo-600' : 'text-gray-400' }}" />
            Dashboard
        </a>
        <a href="{{ route('murid.materi.index') }}" wire:navigate class="{{ request()->routeIs('murid.materi.*') || request()->routeIs('murid.praktikum.*') || request()->routeIs('murid.quiz.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
            <x-heroicon-o-academic-cap class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('murid.materi.*') || request()->routeIs('murid.praktikum.*') || request()->routeIs('murid.quiz.*') ? 'text-indigo-600' : 'text-gray-400' }}" />
            Materi Pembelajaran
        </a>
        
        <div class="pt-4 mt-4 border-t border-gray-100">
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Evaluasi & Progres</p>
            <a href="{{ route('murid.nilai.riwayat') }}" wire:navigate class="{{ request()->routeIs('murid.nilai.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                <x-heroicon-o-chart-bar class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('murid.nilai.*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                Riwayat Nilai
            </a>
            <a href="{{ route('murid.feedback.daftar') }}" wire:navigate class="{{ request()->routeIs('murid.feedback.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                <x-heroicon-o-chat-bubble-left-ellipsis class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('murid.feedback.*') ? 'text-indigo-600' : 'text-gray-400' }}" />
                Feedback Guru
            </a>
        </div>

        <div class="pt-4 mt-4 border-t border-gray-100">
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Gamifikasi</p>
            <a href="{{ route('murid.leaderboard') }}" wire:navigate class="{{ request()->routeIs('murid.leaderboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                <x-heroicon-o-trophy class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('murid.leaderboard') ? 'text-amber-500' : 'text-gray-400' }}" />
                Papan Peringkat
            </a>
            <a href="{{ route('murid.badge') }}" wire:navigate class="{{ request()->routeIs('murid.badge') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                <x-heroicon-o-star class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('murid.badge') ? 'text-amber-500' : 'text-gray-400' }}" />
                Koleksi Badge
            </a>
        </div>
    @endif
</div>
