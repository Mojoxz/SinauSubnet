<div>
{{-- Halaman Beranda SinauSubnet --}}

{{-- ── Hero Section ───────────────────────────────────────────────────────── --}}
<section class="relative overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-sky-50 py-20 sm:py-28 dark:from-gray-900 dark:via-gray-950 dark:to-gray-900">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            {{-- Badge --}}
            <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-4 py-1.5 text-sm font-medium text-indigo-700 dark:border-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                <x-heroicon-s-academic-cap class="h-4 w-4" />
                Platform Pembelajaran Subnetting Berbasis Masalah
            </div>

            {{-- Judul --}}
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl dark:text-white">
                Kuasai <span class="text-indigo-600 dark:text-indigo-400">Subnetting</span><br>
                dengan Cara yang Tepat
            </h1>

            <p class="mx-auto mt-6 max-w-2xl text-lg text-gray-600 dark:text-gray-300">
                SinauSubnet menggunakan pendekatan <strong>Problem-Based Learning</strong> —
                belajar dari kasus nyata, berlatih langsung, dinilai cerdas dengan AI.
            </p>

            <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                        <x-heroicon-o-home class="h-5 w-5" />
                        Ke Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                        <x-heroicon-o-user-plus class="h-5 w-5" />
                        Mulai Belajar
                    </a>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-6 py-3 text-base font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        <x-heroicon-o-arrow-right-on-rectangle class="h-5 w-5" />
                        Sudah Punya Akun?
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>

{{-- ── Fitur Utama ─────────────────────────────────────────────────────────── --}}
<section class="py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-12 text-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Kenapa SinauSubnet?</h2>
            <p class="mt-3 text-gray-500 dark:text-gray-400">Dirancang khusus untuk siswa dan guru SMK Teknik Komputer Jaringan</p>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {{-- Fitur 1 --}}
            <div class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-indigo-200 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                    <x-heroicon-o-puzzle-piece class="h-6 w-6" />
                </div>
                <h3 class="mb-2 font-semibold text-gray-900 dark:text-white">Problem-Based Learning</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Belajar dari studi kasus nyata: mengonfigurasi jaringan perusahaan, sekolah, dan gedung bertingkat.
                </p>
            </div>

            {{-- Fitur 2 --}}
            <div class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-indigo-200 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <x-heroicon-o-cpu-chip class="h-6 w-6" />
                </div>
                <h3 class="mb-2 font-semibold text-gray-900 dark:text-white">Penilaian Cerdas dengan AI</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Jawaban uraian dinilai oleh AI (Gemini) secara konsisten berdasarkan rubrik, guru tetap memvalidasi.
                </p>
            </div>

            {{-- Fitur 3 --}}
            <div class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-indigo-200 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                    <x-heroicon-o-trophy class="h-6 w-6" />
                </div>
                <h3 class="mb-2 font-semibold text-gray-900 dark:text-white">Gamifikasi & Leaderboard</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Kumpulkan poin, raih lencana, dan bersaing di leaderboard kelas untuk memotivasi belajar.
                </p>
            </div>

            {{-- Fitur 4 --}}
            <div class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-indigo-200 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-600 dark:bg-sky-900/30 dark:text-sky-400">
                    <x-heroicon-o-wifi class="h-6 w-6" />
                </div>
                <h3 class="mb-2 font-semibold text-gray-900 dark:text-white">Bekerja Offline</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Font, aset, dan antarmuka dihosting lokal — tetap berjalan sempurna di jaringan lab sekolah.
                </p>
            </div>

            {{-- Fitur 5 --}}
            <div class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-indigo-200 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                    <x-heroicon-o-chart-bar class="h-6 w-6" />
                </div>
                <h3 class="mb-2 font-semibold text-gray-900 dark:text-white">Laporan Komprehensif</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Guru mendapat laporan per murid, per kelas, dan per kompetensi dengan ekspor Excel & PDF.
                </p>
            </div>

            {{-- Fitur 6 --}}
            <div class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-indigo-200 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                    <x-heroicon-o-shield-check class="h-6 w-6" />
                </div>
                <h3 class="mb-2 font-semibold text-gray-900 dark:text-white">Aman & Terkontrol</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Role-based access control, rate limiting, dan validasi data memastikan integritas akademik.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ── Alur Pembelajaran PBL ──────────────────────────────────────────────── --}}
<section class="bg-gray-50 py-20 dark:bg-gray-900">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-12 text-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Alur Pembelajaran</h2>
            <p class="mt-3 text-gray-500 dark:text-gray-400">Tiga tahap terintegrasi mengikuti siklus Problem-Based Learning</p>
        </div>

        <div class="grid gap-8 sm:grid-cols-3">
            <div class="text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg">
                    <x-heroicon-o-book-open class="h-8 w-8" />
                </div>
                <div class="mb-1 text-xs font-bold uppercase tracking-widest text-indigo-500">Tahap 1</div>
                <h3 class="mb-2 text-lg font-bold text-gray-900 dark:text-white">Pelajari Materi</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Baca dan pahami konsep subnetting, VLSM, dan CIDR dari materi terstruktur yang disiapkan guru.
                </p>
            </div>

            <div class="text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-lg">
                    <x-heroicon-o-wrench-screwdriver class="h-8 w-8" />
                </div>
                <div class="mb-1 text-xs font-bold uppercase tracking-widest text-amber-500">Tahap 2</div>
                <h3 class="mb-2 text-lg font-bold text-gray-900 dark:text-white">Praktikum</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Selesaikan studi kasus nyata di Cisco Packet Tracer dan unggah bukti screenshot atau file .pkt.
                </p>
            </div>

            <div class="text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-lg">
                    <x-heroicon-o-pencil-square class="h-8 w-8" />
                </div>
                <div class="mb-1 text-xs font-bold uppercase tracking-widest text-emerald-500">Tahap 3</div>
                <h3 class="mb-2 text-lg font-bold text-gray-900 dark:text-white">Kerjakan Quiz</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Uji pemahaman melalui quiz pilihan ganda, isian, dan uraian yang dinilai otomatis dan oleh AI.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ── CTA Footer ──────────────────────────────────────────────────────────── --}}
@guest
<section class="bg-indigo-600 py-16 dark:bg-indigo-800">
    <div class="mx-auto max-w-3xl px-4 text-center">
        <h2 class="text-3xl font-bold text-white">Siap Mulai Belajar?</h2>
        <p class="mt-4 text-indigo-100">Daftarkan diri sekarang dan mulai perjalanan menguasai subnetting jaringan komputer.</p>
        <a href="{{ route('register') }}"
           class="mt-8 inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-base font-semibold text-indigo-600 shadow transition hover:bg-indigo-50">
            <x-heroicon-o-user-plus class="h-5 w-5" />
            Daftar Gratis Sekarang
        </a>
    </div>
</section>
@endguest

</div>
