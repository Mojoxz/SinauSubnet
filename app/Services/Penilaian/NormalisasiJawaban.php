<?php

declare(strict_types=1);

namespace App\Services\Penilaian;

/**
 * Service untuk menormalisasi jawaban isian singkat agar penilaian
 * lebih toleran terhadap variasi format pengetikan (huruf besar/kecil, spasi ekstra, format slash).
 */
class NormalisasiJawaban
{
    /**
     * Menormalkan string jawaban.
     * Aturan:
     * 1. Trim spasi awal & akhir
     * 2. Ubah ke huruf kecil (lowercase)
     * 3. Rapatkan spasi ganda menjadi spasi tunggal
     * 4. Hapus karakter '/' di awal jika ada (mengatasi input CIDR misal "/27" vs "27")
     */
    public function normalisasi(string $jawaban): string
    {
        // 1 & 2: Trim dan lowercase
        $jawaban = strtolower(trim($jawaban));

        // 3: Rapatkan spasi ganda
        $jawaban = preg_replace('/\s+/', ' ', $jawaban);

        // 4: Ekuivalensi format (hapus slash awalan jika ada)
        if (str_starts_with((string)$jawaban, '/')) {
            $jawaban = ltrim((string)$jawaban, '/');
        }

        return $jawaban;
    }

    /**
     * Mengecek apakah jawaban user benar berdasarkan array variasi kunci jawaban.
     *
     * @param string $jawabanUser Jawaban yang diinput murid
     * @param array $kunciJawabanVariasi Array berisi string variasi jawaban yang dianggap benar
     * @return bool True jika cocok dengan salah satu kunci
     */
    public function cekBenar(string $jawabanUser, array $kunciJawabanVariasi): bool
    {
        $jawabanUserNormal = $this->normalisasi($jawabanUser);

        foreach ($kunciJawabanVariasi as $kunci) {
            if ($jawabanUserNormal === $this->normalisasi((string) $kunci)) {
                return true;
            }
        }

        return false;
    }
}
