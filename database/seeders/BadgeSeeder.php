<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'nama'        => 'First Step Subnetter',
                'deskripsi'   => 'Selamat! Kamu berhasil menyelesaikan kuis pertamamu. Perjalanan menguasai subnetting dimulai dari sini.',
                'ikon'        => '🎯',
                'syarat_poin' => 10,
            ],
            [
                'nama'        => 'Subnet Explorer',
                'deskripsi'   => 'Kamu sudah mulai memahami dasar-dasar pengalamatan IP dan pembagian jaringan.',
                'ikon'        => '🗺️',
                'syarat_poin' => 100,
            ],
            [
                'nama'        => 'VLSM Apprentice',
                'deskripsi'   => 'Kamu mulai menguasai teknik Variable Length Subnet Masking untuk mengalokasikan IP secara efisien.',
                'ikon'        => '📐',
                'syarat_poin' => 250,
            ],
            [
                'nama'        => 'VLSM Master',
                'deskripsi'   => 'Hebat! Kamu telah menguasai VLSM dan mampu merancang skema pengalamatan jaringan yang kompleks.',
                'ikon'        => '🏆',
                'syarat_poin' => 500,
            ],
            [
                'nama'        => 'CIDR Champion',
                'deskripsi'   => 'Kamu memahami Classless Inter-Domain Routing dan penerapannya dalam topologi jaringan nyata.',
                'ikon'        => '🛡️',
                'syarat_poin' => 750,
            ],
            [
                'nama'        => 'Network Architect',
                'deskripsi'   => 'Pencapaian luar biasa! Kamu telah menyelesaikan seluruh modul PBL dengan pemahaman yang komprehensif.',
                'ikon'        => '🌐',
                'syarat_poin' => 1000,
            ],
            [
                'nama'        => 'Speed Demon',
                'deskripsi'   => 'Kamu menyelesaikan kuis dengan sangat cepat dan mendapatkan bonus kecepatan penuh!',
                'ikon'        => '⚡',
                'syarat_poin' => 200,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(
                ['nama' => $badge['nama']],
                $badge
            );
        }

        $this->command->info('✓ ' . count($badges) . ' badge default berhasil dibuat.');
    }
}
