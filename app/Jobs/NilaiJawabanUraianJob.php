<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\JawabanQuiz;
use App\Models\JawabanPraktikum;
use App\Services\Ai\GeminiEvaluationService;
use App\Notifications\JawabanUraianDinilaiNotification;
use App\Notifications\ButuhKoreksiManualNotification;
use App\Notifications\SampelReviewBaruNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NilaiJawabanUraianJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60]; // Exponential backoff in seconds

    protected $tipeModel;
    protected $jawabanId;

    /**
     * Create a new job instance.
     * $tipeModel = 'quiz' | 'praktikum'
     */
    public function __construct(string $tipeModel, int $jawabanId)
    {
        $this->tipeModel = $tipeModel;
        $this->jawabanId = $jawabanId;
    }

    public function handle(GeminiEvaluationService $aiService): void
    {
        $jawaban = $this->tipeModel === 'quiz' 
            ? JawabanQuiz::with(['soalQuiz', 'hasilQuiz.murid.user'])->find($this->jawabanId)
            : JawabanPraktikum::with(['soalPraktikum', 'murid.user'])->find($this->jawabanId);

        if (!$jawaban) return;
        
        $soal = $this->tipeModel === 'quiz' ? $jawaban->soalQuiz : $jawaban->soalPraktikum;
        
        if ($soal->tipe->value !== 'uraian') return;

        // Increment retry count record
        $jawaban->increment('jumlah_percobaan_ai');

        try {
            $hasilAi = $aiService->evaluate(
                $soal->pertanyaan,
                $soal->rubrik ?? [],
                $soal->skor_maks,
                $jawaban->jawaban ?? ''
            );

            // Cek ambang batas config
            $skorRendahThreshold = config('sinausubnet.ai_review.skor_rendah_threshold', 0.30);
            $skorTinggiThreshold = config('sinausubnet.ai_review.skor_tinggi_threshold', 0.95);
            $randomSamplePct = config('sinausubnet.ai_review.random_sample_percentage', 15);

            $skor = $hasilAi['skor_total'] ?? 0;
            $rasioSkor = $skor / max($soal->skor_maks, 1);
            
            // Penentuan apakah perlu sampel acak?
            $isRandomSample = (mt_rand(1, 100) <= $randomSamplePct);

            $status = \App\Enums\StatusPenilaian::DINILAI_AI->value;
            
            // Simpan hasil
            $jawaban->skor_ai = $skor;
            $jawaban->skor_final = $skor;
            $jawaban->detail_skor_ai = $hasilAi['skor_per_kriteria'] ?? null;
            $jawaban->feedback_ai = $hasilAi['alasan'] ?? null;
            $jawaban->feedback_final = $hasilAi['feedback_murid'] ?? null;
            $jawaban->status_penilaian = $status;
            
            // Jika terpilih sampel acak, kunci permanen!
            if ($isRandomSample) {
                $jawaban->sampel_review = true;
            }

            $jawaban->save();

            // Notifikasi ke Murid
            $muridUser = $this->tipeModel === 'quiz' ? $jawaban->hasilQuiz->murid->user : $jawaban->murid->user;
            if ($muridUser) {
                $muridUser->notify(new JawabanUraianDinilaiNotification($this->tipeModel, $jawaban->id));
            }

            // Notifikasi ke Guru jika ini adalah sampel review
            if ($isRandomSample) {
                $guruUsers = \App\Models\User::role('guru')->get();
                foreach ($guruUsers as $guru) {
                    $guru->notify(new SampelReviewBaruNotification($this->tipeModel, $jawaban->id));
                }
            }

            // Jika skor sangat ekstrem, ini akan disorot di panel Guru secara otomatis (berdasarkan threshold)

        } catch (\Exception $e) {
            Log::error("NilaiJawabanUraianJob gagal untuk ID {$this->jawabanId}: " . $e->getMessage());
            
            if ($this->attempts() >= $this->tries) {
                // Jatuh ke perlu manual
                $jawaban->status_penilaian = \App\Enums\StatusPenilaian::PERLU_MANUAL->value;
                $jawaban->save();
                
                // Notifikasi ke guru (akan di-handle dengan broadcast ke role guru, buat class notifikasi butuh koreksi manual)
                $guruUsers = \App\Models\User::role('guru')->get();
                foreach ($guruUsers as $guru) {
                    $guru->notify(new ButuhKoreksiManualNotification($this->tipeModel, $jawaban->id));
                }
            } else {
                // Lempar ke luar agar worker me-retry (memanfaatkan exponential backoff)
                throw $e;
            }
        }
    }
}
