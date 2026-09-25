<?php

declare(strict_types=1);

use App\Models\Materi;
use App\Models\Praktikum;
use App\Models\SoalPraktikum;
use App\Models\JawabanPraktikum;
use Livewire\Livewire;
use App\Livewire\Guru\Materi\Index;

test('Materi dihapus permanen (hard delete) jika belum ada murid yang menjawab turunannya', function () {
    $guru = buatGuru();
    $materi = Materi::factory()->create(['guru_id' => $guru->guru->id]);
    $praktikum = Praktikum::factory()->create(['materi_id' => $materi->id]);

    Livewire::actingAs($guru)
        ->test(Index::class)
        ->call('deleteMateri', $materi->id)
        ->assertHasNoErrors();

    // Data benar-benar hilang dari database
    expect(Materi::find($materi->id))->toBeNull();
    // Jika ada cascade delete DB, praktikum hilang, tapi ini level Eloquent. Setidaknya materi hilang.
});

test('Materi di-soft-archive jika ada murid yang sudah menjawab praktikum anaknya', function () {
    $guru = buatGuru();
    $murid = buatMurid();

    $materi = Materi::factory()->create(['guru_id' => $guru->guru->id, 'is_aktif' => true]);
    $praktikum = Praktikum::factory()->create(['materi_id' => $materi->id, 'is_aktif' => true]);
    $soal = SoalPraktikum::factory()->create(['praktikum_id' => $praktikum->id, 'is_aktif' => true]);
    
    // Murid membuat jawaban
    JawabanPraktikum::factory()->create([
        'soal_praktikum_id' => $soal->id,
        'murid_id' => $murid->murid->id,
    ]);

    Livewire::actingAs($guru)
        ->test(Index::class)
        ->call('deleteMateri', $materi->id)
        ->assertHasNoErrors();

    // Verifikasi Materi dan turunannya TETAP ADA di database tetapi is_aktif = false
    $materiDB = Materi::find($materi->id);
    expect($materiDB)->not->toBeNull();
    expect($materiDB->is_aktif)->toBeFalse();

    $praktikumDB = Praktikum::find($praktikum->id);
    expect($praktikumDB->is_aktif)->toBeFalse();

    $soalDB = SoalPraktikum::find($soal->id);
    expect($soalDB->is_aktif)->toBeFalse();
});
