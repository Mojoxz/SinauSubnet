<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Halaman Publik (tidak perlu login)
|--------------------------------------------------------------------------
| Livewire 3 class-based components digunakan sebagai route action
| melalui sintaks Route::get(..., ComponentClass::class) yang didukung
| oleh Livewire\LivewireManager::route() yang terdaftar sebagai macro.
|--------------------------------------------------------------------------
*/
Route::get('/', \App\Livewire\Public\Beranda::class)->name('beranda');
Route::get('/tentang', \App\Livewire\Public\Tentang::class)->name('tentang');

/*
|--------------------------------------------------------------------------
| Redirect otomatis setelah login berdasarkan role
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isGuru()) {
        return redirect()->route('guru.dashboard');
    }
    if ($user->isMurid()) {
        return redirect()->route('murid.dashboard');
    }
    abort(403, 'Peran tidak dikenali.');
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| Area Guru — middleware: auth + role:guru
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:guru'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {
        Route::get('/dashboard', \App\Livewire\Guru\Dashboard::class)->name('dashboard');

        Route::get('/materi', \App\Livewire\Guru\Materi\Index::class)->name('materi.index');
        Route::get('/materi/buat', \App\Livewire\Guru\Materi\Buat::class)->name('materi.buat');
        Route::get('/materi/{materi}/ubah', \App\Livewire\Guru\Materi\Ubah::class)->name('materi.ubah');
        Route::get('/materi/{materi}', \App\Livewire\Guru\Materi\Detail::class)->name('materi.detail');

        Route::get('/praktikum', \App\Livewire\Guru\Praktikum\Index::class)->name('praktikum.index');
        Route::get('/praktikum/buat', \App\Livewire\Guru\Praktikum\Buat::class)->name('praktikum.buat');
        Route::get('/praktikum/{praktikum}/ubah', \App\Livewire\Guru\Praktikum\Ubah::class)->name('praktikum.ubah');
        Route::get('/praktikum/{praktikum}', \App\Livewire\Guru\Praktikum\Detail::class)->name('praktikum.detail');

        Route::get('/penilaian/uraian', \App\Livewire\Guru\Penilaian\DaftarUraian::class)->name('penilaian.uraian');
        Route::get('/penilaian/psikomotorik/{praktikum}/{murid}', \App\Livewire\Guru\Penilaian\FormPsikomotorik::class)->name('penilaian.psikomotorik');

        Route::get('/quiz', \App\Livewire\Guru\Quiz\Index::class)->name('quiz.index');
        Route::get('/quiz/buat', \App\Livewire\Guru\Quiz\Buat::class)->name('quiz.buat');
        Route::get('/quiz/{quiz}/ubah', \App\Livewire\Guru\Quiz\Ubah::class)->name('quiz.ubah');
        Route::get('/quiz/{quiz}', \App\Livewire\Guru\Quiz\Detail::class)->name('quiz.detail');

        Route::get('/laporan', \App\Livewire\Guru\Laporan\Index::class)->name('laporan.index');
        Route::get('/laporan/ekspor-nilai', \App\Http\Controllers\Guru\EksporNilaiController::class)->name('laporan.ekspor-nilai');

        Route::get('/feedback', \App\Livewire\Guru\Feedback\Index::class)->name('feedback.index');
        Route::get('/profil', \App\Livewire\Guru\Profil::class)->name('profil');
    });

/*
|--------------------------------------------------------------------------
| Area Murid — middleware: auth + role:murid
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:murid'])
    ->prefix('murid')
    ->name('murid.')
    ->group(function () {
        Route::get('/dashboard', \App\Livewire\Murid\Dashboard::class)->name('dashboard');

        Route::get('/materi', \App\Livewire\Murid\Materi\Index::class)->name('materi.index');
        Route::get('/materi/{materi}', \App\Livewire\Murid\Materi\Baca::class)->name('materi.baca');

        Route::get('/praktikum/{praktikum}', \App\Livewire\Murid\Praktikum\Kerjakan::class)->name('praktikum.kerjakan');

        Route::get('/quiz/{quiz}/mulai', \App\Livewire\Murid\Quiz\Mulai::class)->name('quiz.mulai');
        Route::get('/quiz/{quiz}/kerjakan', \App\Livewire\Murid\Quiz\Kerjakan::class)->name('quiz.kerjakan');
        Route::get('/quiz/{hasilQuiz}/hasil', \App\Livewire\Murid\Quiz\Hasil::class)->name('quiz.hasil');

        Route::get('/nilai', \App\Livewire\Murid\Nilai\Riwayat::class)->name('nilai.riwayat');
        Route::get('/nilai/detail/{hasilQuiz}', \App\Livewire\Murid\Nilai\DetailHasilQuiz::class)->name('nilai.detail-quiz');
        Route::get('/nilai/detail-praktikum/{penilaianPraktikum}', \App\Livewire\Murid\Nilai\DetailPraktikum::class)->name('nilai.detail-praktikum');

        Route::get('/leaderboard', \App\Livewire\Murid\Leaderboard::class)->name('leaderboard');
        Route::get('/badge', \App\Livewire\Murid\BadgeKoleksi::class)->name('badge');

        Route::get('/feedback', \App\Livewire\Murid\Feedback\Daftar::class)->name('feedback.daftar');
        Route::get('/profil', \App\Livewire\Murid\Profil::class)->name('profil');
    });

/*
|--------------------------------------------------------------------------
| Download Berkas Bukti Praktikum (private storage)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->get(
    '/storage/bukti/{jawaban}',
    \App\Http\Controllers\BuktiFileController::class
)->name('storage.bukti');

/*
|--------------------------------------------------------------------------
| Auth Routes (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
