<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Feedback;
use App\Models\HasilQuiz;
use App\Models\JawabanPraktikum;
use App\Models\Materi;
use App\Models\Praktikum;
use App\Models\Quiz;
use App\Policies\FeedbackPolicy;
use App\Policies\HasilQuizPolicy;
use App\Policies\JawabanPraktikumPolicy;
use App\Policies\MateriPolicy;
use App\Policies\PraktikumPolicy;
use App\Policies\QuizPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Daftarkan semua Domain Policies ──────────────────────────────────
        Gate::policy(Materi::class,           MateriPolicy::class);
        Gate::policy(Praktikum::class,        PraktikumPolicy::class);
        Gate::policy(Quiz::class,             QuizPolicy::class);
        Gate::policy(JawabanPraktikum::class, JawabanPraktikumPolicy::class);
        Gate::policy(HasilQuiz::class,        HasilQuizPolicy::class);
        Gate::policy(Feedback::class,         FeedbackPolicy::class);

        // StoragePolicy didaftarkan sebagai Gate manual (bukan model-based)
        // karena tidak ada Eloquent model tunggal yang mewakili "file storage".
        // Penggunaan: Gate::allows('download-bukti-file', $jawabanPraktikum)
        Gate::define('download-bukti-file', [\App\Policies\StoragePolicy::class, 'downloadBuktiFile']);
    }
}
