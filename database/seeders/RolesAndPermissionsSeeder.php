<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleUser;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache Spatie agar tidak ada konflik
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Daftarkan semua Permission granular ────────────────────────────
        $permissions = [
            // Materi
            'materi.view', 'materi.create', 'materi.edit', 'materi.delete', 'materi.archive',

            // Praktikum
            'praktikum.view', 'praktikum.create', 'praktikum.edit',
            'praktikum.delete', 'praktikum.archive',

            // Soal Praktikum
            'soal-praktikum.create', 'soal-praktikum.edit',
            'soal-praktikum.delete', 'soal-praktikum.archive',

            // Jawaban Praktikum
            'jawaban-praktikum.view-own',    // Murid — hanya miliknya
            'jawaban-praktikum.view-all',    // Guru — semua murid
            'jawaban-praktikum.create',      // Murid
            'jawaban-praktikum.review',      // Guru — koreksi/validasi AI

            // Quiz
            'quiz.view', 'quiz.create', 'quiz.edit', 'quiz.delete', 'quiz.archive',

            // Soal Quiz
            'soal-quiz.create', 'soal-quiz.edit', 'soal-quiz.delete', 'soal-quiz.archive',

            // Hasil & Jawaban Quiz
            'hasil-quiz.view-own',           // Murid
            'hasil-quiz.view-all',           // Guru
            'jawaban-quiz.review',           // Guru

            // Penilaian Psikomotorik
            'penilaian-praktikum.create', 'penilaian-praktikum.edit',

            // Leaderboard & Badge
            'leaderboard.view', 'badge.view',

            // Laporan & Ekspor
            'laporan.view', 'laporan.export',

            // Feedback
            'feedback.create', 'feedback.edit', 'feedback.delete',
            'feedback.view-own',             // Murid — hanya miliknya
            'feedback.view-all',             // Guru

            // Notifikasi
            'notifikasi.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ── Role GURU ──────────────────────────────────────────────────────
        $roleGuru = Role::firstOrCreate(['name' => RoleUser::GURU->value, 'guard_name' => 'web']);
        $roleGuru->syncPermissions([
            'materi.view', 'materi.create', 'materi.edit', 'materi.delete', 'materi.archive',
            'praktikum.view', 'praktikum.create', 'praktikum.edit', 'praktikum.delete', 'praktikum.archive',
            'soal-praktikum.create', 'soal-praktikum.edit', 'soal-praktikum.delete', 'soal-praktikum.archive',
            'jawaban-praktikum.view-all', 'jawaban-praktikum.review',
            'quiz.view', 'quiz.create', 'quiz.edit', 'quiz.delete', 'quiz.archive',
            'soal-quiz.create', 'soal-quiz.edit', 'soal-quiz.delete', 'soal-quiz.archive',
            'hasil-quiz.view-all', 'jawaban-quiz.review',
            'penilaian-praktikum.create', 'penilaian-praktikum.edit',
            'leaderboard.view', 'badge.view',
            'laporan.view', 'laporan.export',
            'feedback.create', 'feedback.edit', 'feedback.delete', 'feedback.view-all',
            'notifikasi.view',
        ]);

        // ── Role MURID ─────────────────────────────────────────────────────
        $roleMurid = Role::firstOrCreate(['name' => RoleUser::MURID->value, 'guard_name' => 'web']);
        $roleMurid->syncPermissions([
            'materi.view',
            'praktikum.view',
            'jawaban-praktikum.view-own', 'jawaban-praktikum.create',
            'quiz.view',
            'hasil-quiz.view-own',
            'leaderboard.view', 'badge.view',
            'feedback.view-own',
            'notifikasi.view',
        ]);

        $this->command->info('✓ Role & Permission berhasil didaftarkan.');
    }
}
