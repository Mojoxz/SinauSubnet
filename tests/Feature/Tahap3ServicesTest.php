<?php

declare(strict_types=1);

use App\Actions\HitungSkorPoinQuizAction;
use App\Models\HasilQuiz;
use App\Models\JawabanQuiz;
use App\Models\Quiz;
use App\Models\SoalQuiz;
use App\Models\Materi;
use App\Models\Praktikum;
use App\Services\Ai\GeminiEvaluationService;
use App\Services\Penilaian\NormalisasiJawaban;
use App\Services\Penilaian\NilaiCalculator;
use App\Services\Penilaian\PsikomotorikCalculator;
use App\Models\PenilaianPraktikum;
use App\Models\RubrikPraktikum;
use App\Models\NilaiAspek;
use Illuminate\Support\Facades\Http;
use Database\Seeders\RubrikPsikomotorikSeeder;

test('NilaiCalculator menghitung persentase kognitif agregat dengan benar', function () {
    $muridUser = buatMurid();
    $murid = $muridUser->murid; // Get the actual Murid model
    $guruUser = buatGuru();
    $guru = $guruUser->guru;

    $materi = Materi::factory()->create(['guru_id' => $guru->id]);
    $quiz = Quiz::factory()->create(['materi_id' => $materi->id]);
    
    $soal1 = SoalQuiz::factory()->create(['quiz_id' => $quiz->id, 'level_bloom' => 'C3', 'skor_maks' => 10]);
    $soal2 = SoalQuiz::factory()->create(['quiz_id' => $quiz->id, 'level_bloom' => 'C3', 'skor_maks' => 20]);
    $soal3 = SoalQuiz::factory()->create(['quiz_id' => $quiz->id, 'level_bloom' => 'C4', 'skor_maks' => 50]);

    $hasil = HasilQuiz::factory()->create(['quiz_id' => $quiz->id, 'murid_id' => $murid->id]);

    JawabanQuiz::create(['hasil_quiz_id' => $hasil->id, 'soal_quiz_id' => $soal1->id, 'skor_final' => 5, 'jawaban' => 'A', 'skor_ai' => 0, 'status_penilaian' => 'menunggu_ai']);
    JawabanQuiz::create(['hasil_quiz_id' => $hasil->id, 'soal_quiz_id' => $soal2->id, 'skor_final' => 20, 'jawaban' => 'A', 'skor_ai' => 0, 'status_penilaian' => 'menunggu_ai']);
    JawabanQuiz::create(['hasil_quiz_id' => $hasil->id, 'soal_quiz_id' => $soal3->id, 'skor_final' => 25, 'jawaban' => 'A', 'skor_ai' => 0, 'status_penilaian' => 'menunggu_ai']);

    $calc = new NilaiCalculator();
    
    expect($calc->hitungC3($murid))->toBe(83.33);
    expect($calc->hitungC4($murid))->toBe(50.00);
    expect($calc->hitungC5($murid))->toBe(0.00); 
});

test('PsikomotorikCalculator mengonversi 5 aspek skala 4 ke persentase', function () {
    $guruUser = buatGuru();
    $guru = $guruUser->guru;
    $muridUser = buatMurid();
    $murid = $muridUser->murid;

    $materi = Materi::factory()->create(['guru_id' => $guru->id]);
    $praktikum = Praktikum::factory()->create(['materi_id' => $materi->id]);
    
    $this->seed(RubrikPsikomotorikSeeder::class);
    RubrikPraktikum::query()->update(['praktikum_id' => $praktikum->id]);

    $penilaian = PenilaianPraktikum::create(['praktikum_id' => $praktikum->id, 'murid_id' => $murid->id, 'guru_id' => $guru->id]);
    
    $rubriks = RubrikPraktikum::where('praktikum_id', $praktikum->id)->get();
    
    $skorBeragam = [4, 3, 4, 2, 3];
    foreach ($rubriks as $index => $rubrik) {
        NilaiAspek::create(['penilaian_praktikum_id' => $penilaian->id, 'rubrik_praktikum_id' => $rubrik->id, 'skor' => $skorBeragam[$index]]);
    }

    $calc = new PsikomotorikCalculator();
    expect($calc->hitungNilaiPraktikum($penilaian))->toBe(80.00);
});

test('HitungSkorPoinQuizAction otomatis menilai PG dan Isian sekaligus bonus kecepatan', function () {
    $guruUser = buatGuru();
    $guru = $guruUser->guru;
    $muridUser = buatMurid();
    $murid = $muridUser->murid;

    $materi = Materi::factory()->create(['guru_id' => $guru->id]);
    $quiz = Quiz::factory()->create(['materi_id' => $materi->id, 'durasi_menit' => 10, 'bonus_kecepatan_maks' => 50]);
    $hasil = HasilQuiz::factory()->create([
        'quiz_id' => $quiz->id, 
        'murid_id' => $murid->id,
        'waktu_mulai' => now()->subMinutes(5),
        'waktu_selesai' => now(), 
    ]);

    $soal1 = SoalQuiz::factory()->create([
        'quiz_id' => $quiz->id, 'tipe' => 'pilihan_ganda', 'kunci_jawaban' => ['192.168.1.0'], 'skor_maks' => 20, 'poin_dasar' => 100
    ]);
    JawabanQuiz::create(['hasil_quiz_id' => $hasil->id, 'soal_quiz_id' => $soal1->id, 'jawaban' => '192.168.1.0', 'skor_ai' => 0, 'skor_final' => 0, 'status_penilaian' => 'menunggu_ai']);

    $soal2 = SoalQuiz::factory()->create([
        'quiz_id' => $quiz->id, 'tipe' => 'isian', 'kunci_jawaban' => ['27'], 'skor_maks' => 20, 'poin_dasar' => 100
    ]);
    JawabanQuiz::create(['hasil_quiz_id' => $hasil->id, 'soal_quiz_id' => $soal2->id, 'jawaban' => ' /27 ', 'skor_ai' => 0, 'skor_final' => 0, 'status_penilaian' => 'menunggu_ai']);

    $soal3 = SoalQuiz::factory()->create([
        'quiz_id' => $quiz->id, 'tipe' => 'uraian', 'skor_maks' => 60, 'poin_dasar' => 300
    ]);
    JawabanQuiz::create(['hasil_quiz_id' => $hasil->id, 'soal_quiz_id' => $soal3->id, 'jawaban' => 'Jawaban panjang...', 'skor_ai' => 0, 'skor_final' => 0, 'status_penilaian' => 'menunggu_ai']);

    $action = new HitungSkorPoinQuizAction(new NormalisasiJawaban());
    $action->execute($hasil);

    $hasil->refresh();
    
    expect($hasil->skor_total)->toEqual(40);
    expect($hasil->poin_total)->toBe(225);
    
    $j1 = JawabanQuiz::where('soal_quiz_id', $soal1->id)->first();
    expect($j1->status_penilaian->value)->toBe('final');

    $j3 = JawabanQuiz::where('soal_quiz_id', $soal3->id)->first();
    expect($j3->status_penilaian->value)->toBe('menunggu_ai');
});

test('GeminiEvaluationService memparsing JSON murni dengan tepat', function () {
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => "```json\n{\n  \"skor_per_kriteria\": {\n    \"kriteria_1\": 10\n  },\n  \"skor_total\": 10,\n  \"alasan\": \"Bagus\",\n  \"feedback_murid\": \"Sangat baik\"\n}\n```"]
                        ]
                    ]
                ]
            ]
        ], 200)
    ]);

    $service = new GeminiEvaluationService();
    $hasil = $service->evaluate('Pertanyaan', 'Jawaban', ['kriteria_1' => 10], 10);
    
    expect($hasil['skor_total'])->toBe(10);
    expect($hasil['alasan'])->toBe('Bagus');
});
