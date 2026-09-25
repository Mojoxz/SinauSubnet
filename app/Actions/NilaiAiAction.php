<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\JawabanPraktikum;
use App\Models\JawabanQuiz;
use App\Services\Ai\GeminiEvaluationService;
use App\Enums\StatusPenilaian;
use Illuminate\Support\Facades\Log;

class NilaiAiAction
{
    public function __construct(
        private GeminiEvaluationService $geminiService
    ) {}

    /**
     * Memproses jawaban Praktikum atau Quiz via AI.
     */
    public function execute(JawabanPraktikum|JawabanQuiz $jawaban): void
    {
        $soal = $jawaban instanceof JawabanPraktikum ? $jawaban->soalPraktikum : $jawaban->soalQuiz;
        
        if (! $soal->rubrik) {
            Log::warning('Soal uraian tidak memiliki rubrik.', ['soal_id' => $soal->id]);
            $this->fallbackKeManual($jawaban);
            return;
        }

        try {
            $hasilAi = $this->geminiService->evaluate(
                $soal->pertanyaan,
                $jawaban->jawaban,
                $soal->rubrik,
                (float) $soal->skor_maks
            );

            $skorAi = (float) $hasilAi['skor_total'];
            
            // Tentukan status. Jika terlalu ekstrem (misal ambang <30% atau >95%), mungkin butuh review
            // Tapi secara default kita set ke DINILAI_AI. Flag ekstrem akan difilter di query Guru.
            
            $jawaban->update([
                'skor_ai' => $skorAi,
                'skor_final' => $skorAi, // Default ke skor AI dulu sebelum di-override Guru
                'detail_skor_ai' => $hasilAi['skor_per_kriteria'] ?? [],
                'feedback_ai' => $hasilAi['feedback_murid'] ?? null,
                'feedback_final' => $hasilAi['feedback_murid'] ?? null,
                'status_penilaian' => StatusPenilaian::DINILAI_AI,
            ]);

            // Penetapan Sampel Acak Permanen (15%)
            $randomThreshold = config('sinausubnet.random_sample_percentage', 15);
            if (rand(1, 100) <= $randomThreshold) {
                $jawaban->update(['sampel_review' => true]);
            }

        } catch (\Exception $e) {
            $percobaan = $jawaban->jumlah_percobaan_ai + 1;
            $jawaban->update(['jumlah_percobaan_ai' => $percobaan]);
            
            if ($percobaan >= config('sinausubnet.gemini.max_retries', 3)) {
                $this->fallbackKeManual($jawaban);
            } else {
                throw $e; // Lempar agar Job diretry oleh framework Queue Laravel
            }
        }
    }

    private function fallbackKeManual(JawabanPraktikum|JawabanQuiz $jawaban): void
    {
        $jawaban->update([
            'status_penilaian' => StatusPenilaian::PERLU_MANUAL,
        ]);
        
        // TODO: (Tahap 6) Dispatch notification ke Guru bahwa ada jawaban perlu koreksi manual
    }
}
