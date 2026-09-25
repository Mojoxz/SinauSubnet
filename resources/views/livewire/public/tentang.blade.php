<div>
{{-- Halaman Tentang & Fitur SinauSubnet --}}

{{-- Header --}}
<section class="bg-gradient-to-br from-indigo-50 to-white py-16 dark:from-gray-900 dark:to-gray-950">
    <div class="mx-auto max-w-4xl px-4 text-center">
        <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-4 py-1.5 text-sm font-medium text-indigo-700 dark:border-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
            <x-heroicon-o-information-circle class="h-4 w-4" />
            Tentang Aplikasi
        </div>
        <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white">Tentang SinauSubnet</h1>
        <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">
            Platform e-learning subnetting jaringan komputer berbasis Problem-Based Learning
            untuk siswa dan guru SMK program keahlian Teknik Komputer Jaringan.
        </p>
    </div>
</section>

{{-- Detail Fitur --}}
<section class="py-16">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

        {{-- Latar Belakang --}}
        <div class="mb-16 rounded-2xl border border-gray-200 bg-white p-8 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                    <x-heroicon-o-light-bulb class="h-5 w-5" />
                </div>
                <div>
                    <h2 class="mb-3 text-xl font-bold text-gray-900 dark:text-white">Latar Belakang</h2>
                    <p class="text-gray-600 dark:text-gray-300">
                        Subnetting adalah salah satu kompetensi inti di mata pelajaran Administrasi Jaringan SMK.
                        Namun, banyak siswa masih kesulitan memahami konsep abstrak seperti VLSM dan CIDR karena
                        kurangnya praktik langsung dan umpan balik yang cepat. SinauSubnet hadir sebagai solusi
                        dengan mengintegrasikan teori, praktik berbasis kasus, dan evaluasi berbantuan AI dalam
                        satu platform yang berjalan penuh secara offline di jaringan lab sekolah.
                    </p>
                </div>
            </div>
        </div>

        {{-- Matriks Fitur --}}
        <h2 class="mb-8 text-2xl font-bold text-gray-900 dark:text-white">Fitur Lengkap</h2>

        <div class="grid gap-6 md:grid-cols-2">

            {{-- Fitur Murid --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 text-sky-600 dark:bg-sky-900/30 dark:text-sky-400">
                        <x-heroicon-o-user class="h-5 w-5" />
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white">Untuk Siswa</h3>
                </div>
                <ul class="space-y-3">
                    @foreach ([
                        ['Baca materi bertingkat (Level 1-4) dengan konten Markdown terformat', 'book-open'],
                        ['Kerjakan praktikum studi kasus dan unggah bukti (.pkt / screenshot)', 'wrench-screwdriver'],
                        ['Ikuti quiz dengan timer, pilihan ganda, isian, dan uraian', 'pencil-square'],
                        ['Lihat hasil dan koreksi detail per soal dari AI + Guru', 'document-magnifying-glass'],
                        ['Pantau progress PBL per materi (Materi → Praktikum → Quiz)', 'chart-bar'],
                        ['Kumpulkan poin, raih lencana, dan lihat peringkat leaderboard', 'trophy'],
                        ['Baca feedback personal dari guru', 'chat-bubble-left-ellipsis'],
                    ] as [$text, $icon])
                    <li class="flex items-start gap-2.5">
                        <x-dynamic-component :component="'heroicon-s-check-circle'" class="mt-0.5 h-4 w-4 flex-shrink-0 text-emerald-500" />
                        <span class="text-sm text-gray-600 dark:text-gray-300">{{ $text }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Fitur Guru --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                        <x-heroicon-o-academic-cap class="h-5 w-5" />
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white">Untuk Guru</h3>
                </div>
                <ul class="space-y-3">
                    @foreach ([
                        ['Buat & kelola materi, praktikum, soal, dan quiz dengan antarmuka visual', 'squares-plus'],
                        ['Tinjau dan validasi/koreksi hasil penilaian AI (inter-rater reliability)', 'shield-check'],
                        ['Nilai unjuk kerja psikomotorik dengan rubrik 5 aspek (skala 1-4)', 'clipboard-document-check'],
                        ['Kirim feedback personal tertulis ke murid', 'chat-bubble-left-ellipsis'],
                        ['Ekspor laporan nilai ke Excel dan PDF', 'arrow-down-tray'],
                        ['Notifikasi otomatis untuk jawaban perlu koreksi manual', 'bell'],
                        ['Dashboard statistik kelas: rata-rata nilai, distribusi, progress', 'chart-pie'],
                    ] as [$text, $icon])
                    <li class="flex items-start gap-2.5">
                        <x-heroicon-s-check-circle class="mt-0.5 h-4 w-4 flex-shrink-0 text-indigo-500" />
                        <span class="text-sm text-gray-600 dark:text-gray-300">{{ $text }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Stack Teknologi --}}
        <div class="mt-12">
            <h2 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">Stack Teknologi</h2>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ([
                    ['Laravel 12', 'Backend framework'],
                    ['Livewire 3', 'Komponen reaktif'],
                    ['Tailwind CSS', 'Styling utility-first'],
                    ['Gemini AI', 'Penilaian uraian'],
                    ['MySQL 8', 'Database'],
                    ['IBM Plex', 'Tipografi'],
                ] as [$nama, $desc])
                <div class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-center dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $nama }}</p>
                    <p class="mt-0.5 text-xs text-gray-400">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

</div>
