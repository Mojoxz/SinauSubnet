<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Praktikum;
use App\Models\RubrikPraktikum;
use Illuminate\Database\Seeder;

/**
 * Seeder ini membuat 5 aspek rubrik psikomotorik default
 * untuk setiap Praktikum yang belum memiliki rubrik.
 *
 * Dijalankan setelah ada data Praktikum, atau dipanggil dari
 * event/observer saat Praktikum baru dibuat oleh Guru.
 */
class RubrikPsikomotorikSeeder extends Seeder
{
    /**
     * 5 Aspek unjuk kerja standar penilaian psikomotorik Subnetting.
     * Masing-masing menggunakan skala 1-4.
     */
    public static array $aspekDefault = [
        [
            'aspek'    => 'Ketepatan Tabel Subnet',
            'indikator'=> '4=Network, Broadcast, Host Range semua benar; 3=≤1 kesalahan kecil; 2=≤2 kesalahan; 1=Mayoritas salah atau kosong.',
            'urutan'   => 1,
        ],
        [
            'aspek'    => 'Konfigurasi IP & Subnet Mask',
            'indikator'=> '4=Semua perangkat dikonfigurasi IP dan mask sesuai desain; 3=≤1 perangkat salah; 2=≤2 perangkat salah; 1=Konfigurasi tidak selesai.',
            'urutan'   => 2,
        ],
        [
            'aspek'    => 'Keberhasilan Uji Konektivitas',
            'indikator'=> '4=Ping dalam dan antar-subnet semua berhasil; 3=Hanya ping dalam subnet berhasil; 2=Sebagian koneksi berhasil; 1=Tidak ada koneksi berhasil.',
            'urutan'   => 3,
        ],
        [
            'aspek'    => 'Dokumentasi & Laporan Praktik',
            'indikator'=> '4=Bukti lengkap, berurutan, terbaca jelas, dan diunggah tepat waktu; 3=Lengkap tapi kurang rapi; 2=Tidak lengkap; 1=Tidak ada dokumentasi.',
            'urutan'   => 4,
        ],
        [
            'aspek'    => 'Kemandirian & Ketepatan Waktu',
            'indikator'=> '4=Mandiri penuh, selesai sebelum waktu habis; 3=Mandiri dengan sedikit bantuan; 2=Perlu beberapa kali bimbingan; 1=Bergantung penuh pada guru.',
            'urutan'   => 5,
        ],
    ];

    public function run(): void
    {
        $praktikums = Praktikum::doesntHave('rubrikPraktikums')->get();

        if ($praktikums->isEmpty()) {
            $this->command->warn('Tidak ada Praktikum tanpa rubrik yang perlu diisi.');
            return;
        }

        foreach ($praktikums as $praktikum) {
            foreach (self::$aspekDefault as $aspek) {
                RubrikPraktikum::firstOrCreate(
                    [
                        'praktikum_id' => $praktikum->id,
                        'aspek'        => $aspek['aspek'],
                    ],
                    [
                        'indikator' => $aspek['indikator'],
                        'urutan'    => $aspek['urutan'],
                    ]
                );
            }
        }

        $this->command->info("✓ Rubrik psikomotorik default diterapkan ke {$praktikums->count()} praktikum.");
    }
}
