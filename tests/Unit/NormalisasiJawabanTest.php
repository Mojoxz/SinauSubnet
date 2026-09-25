<?php

declare(strict_types=1);

use App\Services\Penilaian\NormalisasiJawaban;

beforeEach(function () {
    $this->service = new NormalisasiJawaban();
});

test('menormalkan spasi ekstra dan huruf kapital', function () {
    $hasil = $this->service->normalisasi('   255.255.255.0  ');
    expect($hasil)->toBe('255.255.255.0');

    $hasil = $this->service->normalisasi('Class C');
    expect($hasil)->toBe('class c');

    $hasil = $this->service->normalisasi('   Kelas    C   ');
    expect($hasil)->toBe('kelas c');
});

test('mentoleransi format CIDR dengan slash', function () {
    $hasil1 = $this->service->normalisasi('/27');
    $hasil2 = $this->service->normalisasi('27');
    
    expect($hasil1)->toBe('27')
        ->and($hasil2)->toBe('27')
        ->and($hasil1)->toBe($hasil2);
});

test('pengecekan benar salah mendukung variasi kunci jawaban', function () {
    $kunci = ['192.168.1.0', '192.168.1.0/24'];

    // Jawaban persis
    expect($this->service->cekBenar('192.168.1.0', $kunci))->toBeTrue();
    
    // Jawaban dengan spasi
    expect($this->service->cekBenar('  192.168.1.0/24  ', $kunci))->toBeTrue();

    // Jawaban dengan kapital acak (meski ini angka, kita test behaviornya)
    expect($this->service->cekBenar(' 192.168.1.0 ', $kunci))->toBeTrue();

    // Jawaban salah
    expect($this->service->cekBenar('192.168.1.255', $kunci))->toBeFalse();
    expect($this->service->cekBenar('10.0.0.0', $kunci))->toBeFalse();
});

test('ekuivalensi slash cidr dalam cekBenar', function () {
    $kunci = ['28']; // Hanya angka 28

    // Murid menjawab dengan '/28' harusnya dinilai benar
    expect($this->service->cekBenar('/28', $kunci))->toBeTrue();
    expect($this->service->cekBenar('28', $kunci))->toBeTrue();
    
    // Jawaban salah
    expect($this->service->cekBenar('/29', $kunci))->toBeFalse();
});
