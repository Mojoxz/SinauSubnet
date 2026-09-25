<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal pengecekan kuis kedaluwarsa secara otomatis
Schedule::command('sinausubnet:finalisasi-quiz-kedaluwarsa')->everyMinute();

