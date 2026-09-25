<?php

declare(strict_types=1);

use App\Enums\StatusPenilaian;
use App\Models\HasilQuiz;
use App\Models\JawabanPraktikum;
use App\Models\JawabanQuiz;
use App\Models\Materi;
use App\Models\Murid;
use App\Models\Praktikum;
use App\Models\Quiz;
use App\Models\RiwayatPoin;
use App\Models\SoalPraktikum;
use App\Models\SoalQuiz;
use App\Models\User;

// ────────────────────────────────────────────────────────────────────────────
// RiwayatPoinLedgerTest: unique constraint mencegah duplikasi poin
// ────────────────────────────────────────────────────────────────────────────

test('riwayat_poin menolak penulisan duplikat untuk sumber yang sama', function () {
    $murid = buatMurid();
    $muridModel = $murid->murid;

    $hasilQuiz = HasilQuiz::factory()->create(['murid_id' => $muridModel->id]);

    // Tulis pertama kali — harus berhasil
    RiwayatPoin::create([
        'murid_id'    => $muridModel->id,
        'sumber_type' => HasilQuiz::class,
        'sumber_id'   => $hasilQuiz->id,
        'poin'        => 50,
        'keterangan'  => 'Poin kuis pertama',
    ]);

    // Tulis kedua kali dengan sumber yang sama — harus melempar exception
    expect(fn () => RiwayatPoin::create([
        'murid_id'    => $muridModel->id,
        'sumber_type' => HasilQuiz::class,
        'sumber_id'   => $hasilQuiz->id,
        'poin'        => 50,
        'keterangan'  => 'Duplikat — seharusnya ditolak',
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

test('riwayat_poin mengizinkan sumber berbeda untuk murid yang sama', function () {
    $murid = buatMurid();
    $muridModel = $murid->murid;

    $hasilQuiz1 = HasilQuiz::factory()->create(['murid_id' => $muridModel->id]);
    $hasilQuiz2 = HasilQuiz::factory()->create(['murid_id' => $muridModel->id]);

    RiwayatPoin::create(['murid_id' => $muridModel->id, 'sumber_type' => HasilQuiz::class, 'sumber_id' => $hasilQuiz1->id, 'poin' => 30]);
    RiwayatPoin::create(['murid_id' => $muridModel->id, 'sumber_type' => HasilQuiz::class, 'sumber_id' => $hasilQuiz2->id, 'poin' => 40]);

    expect(RiwayatPoin::where('murid_id', $muridModel->id)->count())->toBe(2);
});

// ────────────────────────────────────────────────────────────────────────────
// PreventHardDeleteWhenAnsweredTest: Soft-archive pada Quiz/Praktikum terjawab
// ────────────────────────────────────────────────────────────────────────────

test('quiz yang sudah memiliki hasil tidak bisa di-hard delete (Policy menolak)', function () {
    $guru = buatGuru();
    $murid = buatMurid();

    $materi = Materi::factory()->create(['guru_id' => $guru->guru->id]);
    $quiz   = Quiz::factory()->create(['materi_id' => $materi->id]);

    // Simulasikan murid sudah mengerjakan kuis
    HasilQuiz::factory()->create([
        'quiz_id'  => $quiz->id,
        'murid_id' => $murid->murid->id,
    ]);

    // Quiz sekarang memiliki jawaban murid
    expect($quiz->memilikiJawabanMurid())->toBeTrue();

    // Policy harus menolak hard delete
    expect($guru->can('delete', $quiz))->toBeFalse();
});

test('quiz yang belum memiliki hasil boleh di-hard delete (Policy mengizinkan)', function () {
    $guru  = buatGuru();
    $materi = Materi::factory()->create(['guru_id' => $guru->guru->id]);
    $quiz  = Quiz::factory()->create(['materi_id' => $materi->id]);

    expect($quiz->memilikiJawabanMurid())->toBeFalse();
    expect($guru->can('delete', $quiz))->toBeTrue();
});

test('praktikum yang sudah memiliki jawaban tidak bisa di-hard delete', function () {
    $guru   = buatGuru();
    $murid  = buatMurid();
    $materi = Materi::factory()->create(['guru_id' => $guru->guru->id]);

    $praktikum = Praktikum::factory()->create(['materi_id' => $materi->id]);
    $soal      = SoalPraktikum::factory()->create(['praktikum_id' => $praktikum->id]);

    JawabanPraktikum::factory()->create([
        'soal_praktikum_id' => $soal->id,
        'murid_id'          => $murid->murid->id,
    ]);

    expect($praktikum->memilikiJawabanMurid())->toBeTrue();
    expect($guru->can('delete', $praktikum))->toBeFalse();
});

// ────────────────────────────────────────────────────────────────────────────
// PreventHardDeleteMateriWithAnsweredChildrenTest
// ────────────────────────────────────────────────────────────────────────────

test('materi dengan quiz terjawab tidak bisa di-hard delete', function () {
    $guru   = buatGuru();
    $murid  = buatMurid();
    $materi = Materi::factory()->create(['guru_id' => $guru->guru->id]);
    $quiz   = Quiz::factory()->create(['materi_id' => $materi->id]);

    HasilQuiz::factory()->create([
        'quiz_id'  => $quiz->id,
        'murid_id' => $murid->murid->id,
    ]);

    // Materi harus mendeteksi bahwa quiz anaknya sudah dijawab
    expect($materi->memilikiJawabanMurid())->toBeTrue();
    expect($guru->can('delete', $materi))->toBeFalse();
});

test('materi tanpa anak terjawab boleh di-hard delete', function () {
    $guru   = buatGuru();
    $materi = Materi::factory()->create(['guru_id' => $guru->guru->id]);

    expect($materi->memilikiJawabanMurid())->toBeFalse();
    expect($guru->can('delete', $materi))->toBeTrue();
});

// ────────────────────────────────────────────────────────────────────────────
// StatusPenilaian Enum Helper Methods
// ────────────────────────────────────────────────────────────────────────────

test('StatusPenilaian::badgeClass mengembalikan string Tailwind yang tidak kosong', function () {
    foreach (StatusPenilaian::cases() as $status) {
        expect($status->badgeClass())->toBeString()->not->toBeEmpty();
    }
});

test('StatusPenilaian::colorHex mengembalikan format hex yang valid', function () {
    foreach (StatusPenilaian::cases() as $status) {
        expect($status->colorHex())->toMatch('/^#[0-9A-Fa-f]{6}$/');
    }
});

test('StatusPenilaian::label mengembalikan string Bahasa Indonesia', function () {
    expect(StatusPenilaian::FINAL_OTOMATIS->label())->toBe('Dinilai Otomatis');
    expect(StatusPenilaian::MENUNGGU_AI->label())->toBe('Menunggu AI');
    expect(StatusPenilaian::PERLU_MANUAL->label())->toBe('Perlu Koreksi Manual');
});
