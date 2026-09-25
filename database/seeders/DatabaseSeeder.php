<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Guru;
use App\Models\Murid;
use App\Models\Materi;
use App\Models\Praktikum;
use App\Models\SoalPraktikum;
use App\Models\RubrikPraktikum;
use App\Models\Quiz;
use App\Models\SoalQuiz;
use App\Models\Badge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Setup Roles
        $roleGuru = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $roleMurid = Role::firstOrCreate(['name' => 'murid', 'guard_name' => 'web']);

        // 2. Setup Dummy Guru
        $userGuru = User::firstOrCreate(
            ['email' => 'guru@sinausubnet.com'],
            [
                'name' => 'Bapak Budi (Guru TKJ)',
                'password' => Hash::make('password'),
            ]
        );
        $userGuru->assignRole($roleGuru);
        $guru = Guru::firstOrCreate(['user_id' => $userGuru->id], ['nip' => '198001012005011001']);

        // 3. Setup Dummy Murid
        $muridsData = [
            ['name' => 'Andi Siswa', 'email' => 'andi@siswa.com', 'nis' => '1001', 'kelas' => 'XI TKJ 1'],
            ['name' => 'Budi Murid', 'email' => 'budi@siswa.com', 'nis' => '1002', 'kelas' => 'XI TKJ 1'],
            ['name' => 'Citra Pelajar', 'email' => 'citra@siswa.com', 'nis' => '1003', 'kelas' => 'XI TKJ 2'],
        ];

        foreach ($muridsData as $mData) {
            $u = User::firstOrCreate(
                ['email' => $mData['email']],
                [
                    'name' => $mData['name'],
                    'password' => Hash::make('password'),
                ]
            );
            $u->assignRole($roleMurid);
            Murid::firstOrCreate(
                ['user_id' => $u->id],
                [
                    'kelas' => $mData['kelas'],
                    'total_poin' => 0
                ]
            );
        }

        // 4. Setup Badges (Gamifikasi)
        Badge::firstOrCreate(['nama' => 'Pionir Subnet'], [
            'deskripsi' => 'Disematkan pada siswa yang mencapai 500 poin pertama.',
            'syarat_poin' => 500,
            'ikon' => 'badges/pionir.png'
        ]);
        Badge::firstOrCreate(['nama' => 'Master IPv4'], [
            'deskripsi' => 'Ahli dalam memecahkan soal kognitif C4 dan C5.',
            'syarat_poin' => 1500,
            'ikon' => 'badges/master.png'
        ]);

        // 5. Setup Materi Level 1
        $materi1 = Materi::firstOrCreate(['urutan' => 1, 'level' => 1], [
            'guru_id' => $guru->id,
            'judul' => 'Pengenalan IPv4 & Subnetting Dasar',
            'konten' => 'Subnetting adalah teknik memecah jaringan besar menjadi jaringan yang lebih kecil. Konsep dasar ini menggunakan netmask untuk membedakan Network ID dan Host ID.',
            'is_aktif' => true,
        ]);

        // 6. Setup Praktikum PBL Level 1
        $praktikum1 = Praktikum::firstOrCreate(['materi_id' => $materi1->id], [
            'judul' => 'Kasus: Lab Komputer Sekolah',
            'studi_kasus' => 'Sekolah Anda mendapatkan alokasi IP 192.168.1.0/24. Kepala Sekolah meminta Anda membagi jaringan ini untuk 3 Lab yang masing-masing butuh 30 komputer. Rancanglah subnetnya dan simulasikan di Packet Tracer.',
            'is_aktif' => true,
        ]);

        // Rubrik Praktikum (Psikomotorik)
        $rubriks = [
            ['aspek' => 'Konfigurasi IP pada PC', 'indikator' => 'PC mendapatkan IP sesuai subnet yang dirancang.', 'urutan' => 1],
            ['aspek' => 'Konfigurasi Router', 'indikator' => 'Gateway pada router dikonfigurasi dengan benar.', 'urutan' => 2],
            ['aspek' => 'Uji Konektivitas (Ping)', 'indikator' => 'PC antar Lab tidak bisa saling ping (terisolasi/routing terbatas).', 'urutan' => 3],
        ];
        foreach ($rubriks as $rubrik) {
            RubrikPraktikum::firstOrCreate(
                ['praktikum_id' => $praktikum1->id, 'aspek' => $rubrik['aspek']],
                $rubrik
            );
        }

        // Soal Praktikum (Kognitif dari Studi Kasus)
        SoalPraktikum::firstOrCreate(['praktikum_id' => $praktikum1->id, 'urutan' => 1], [
            'pertanyaan' => 'Berdasarkan skenario Lab, prefix berapakah yang paling optimal digunakan untuk memenuhi kebutuhan 30 host per subnet? Jelaskan alasan Anda secara analitis!',
            'tipe' => 'uraian',
            'level_bloom' => 'C4',
            'skor_maks' => 50,
            'kunci_jawaban' => ['/27', '30 host'],
            'rubrik' => [
                ['aspek' => 'Ketepatan Pemilihan Prefix (/27)', 'skor' => 20],
                ['aspek' => 'Penjelasan Matematis (2^5 - 2 = 30)', 'skor' => 30]
            ]
        ]);
        
        SoalPraktikum::firstOrCreate(['praktikum_id' => $praktikum1->id, 'urutan' => 2], [
            'pertanyaan' => 'Unggah file .pkt Anda yang membuktikan desain jaringan ini berjalan dengan baik, lalu tuliskan kesimpulan analisis tabel routing Anda.',
            'tipe' => 'uraian',
            'level_bloom' => 'C3',
            'skor_maks' => 50,
            'kunci_jawaban' => []
        ]);

        // 7. Setup Kuis Evaluasi Level 1
        $quiz1 = Quiz::firstOrCreate(['materi_id' => $materi1->id], [
            'judul' => 'Evaluasi Subnetting Dasar',
            'durasi_menit' => 30,
            'bonus_kecepatan_maks' => 50,
            'is_aktif' => true,
        ]);

        SoalQuiz::firstOrCreate(['quiz_id' => $quiz1->id, 'urutan' => 1], [
            'pertanyaan' => 'Berapa jumlah host yang dapat digunakan (usable host) pada prefix /26?',
            'tipe' => 'pilihan_ganda',
            'level_bloom' => 'C3',
            'opsi' => ['64', '62', '32', '30'],
            'kunci_jawaban' => ['62'],
            'skor_maks' => 20,
            'poin_dasar' => 10,
            'pembahasan' => 'Rumus usable host = 2^(32-26) - 2 = 64 - 2 = 62.'
        ]);

        SoalQuiz::firstOrCreate(['quiz_id' => $quiz1->id, 'urutan' => 2], [
            'pertanyaan' => 'Evaluasi pernyataan berikut: "Penggunaan prefix /24 untuk sebuah divisi yang hanya berisi 10 komputer adalah efisien." Apakah Anda setuju? Berikan argumen.',
            'tipe' => 'uraian',
            'level_bloom' => 'C5',
            'skor_maks' => 80,
            'poin_dasar' => 40,
            'kunci_jawaban' => [],
            'rubrik' => [
                ['aspek' => 'Ketepatan Posisi (Tidak Setuju)', 'skor' => 20],
                ['aspek' => 'Argumen Pemborosan IP (Wasted IP)', 'skor' => 40],
                ['aspek' => 'Saran Prefix Alternatif (misal /28)', 'skor' => 20],
            ],
            'pembahasan' => 'Penggunaan /24 sangat tidak efisien (wasting 244 IP).'
        ]);
        
        $this->command->info('Database berhasil di-seed dengan data Guru, Murid, Materi, Quiz, dan Praktikum!');
    }
}
