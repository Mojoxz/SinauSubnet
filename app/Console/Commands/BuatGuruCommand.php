<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\RoleUser;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Command interaktif untuk mendaftarkan akun Guru secara manual.
 * Akun Guru tidak dapat mendaftar sendiri melalui form registrasi publik.
 *
 * Penggunaan: php artisan sinausubnet:buat-guru
 */
class BuatGuruCommand extends Command
{
    protected $signature = 'sinausubnet:buat-guru';

    protected $description = 'Buat akun Guru baru secara manual (interaktif)';

    public function handle(): int
    {
        $this->info('=== Pendaftaran Akun Guru SinauSubnet ===');
        $this->newLine();

        // --- Kumpulkan input ---
        $nama = $this->ask('Nama lengkap guru');

        $email = $this->ask('Alamat email');
        if (User::where('email', $email)->exists()) {
            $this->error("Email '{$email}' sudah terdaftar.");
            return self::FAILURE;
        }

        $nip = $this->ask('NIP (Nomor Induk Pegawai, opsional — tekan Enter untuk melewati)', '');

        $password = $this->secret('Password (minimal 8 karakter)');
        $konfirmasi = $this->secret('Konfirmasi password');

        // --- Validasi ---
        $validator = Validator::make(
            ['nama' => $nama, 'email' => $email, 'password' => $password, 'konfirmasi' => $konfirmasi],
            [
                'nama'      => ['required', 'string', 'max:255'],
                'email'     => ['required', 'email', 'max:255'],
                'password'  => ['required', 'min:8'],
                'konfirmasi'=> ['required', 'same:password'],
            ],
            [
                'konfirmasi.same' => 'Konfirmasi password tidak cocok.',
                'password.min'    => 'Password minimal 8 karakter.',
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        // --- Tampilkan ringkasan & minta konfirmasi ---
        $this->newLine();
        $this->table(
            ['Field', 'Nilai'],
            [
                ['Nama',  $nama],
                ['Email', $email],
                ['NIP',   $nip ?: '(tidak diisi)'],
            ]
        );

        if (! $this->confirm('Simpan akun guru dengan data di atas?', true)) {
            $this->warn('Pembuatan akun dibatalkan.');
            return self::FAILURE;
        }

        // --- Simpan dalam transaksi ---
        DB::transaction(function () use ($nama, $email, $password, $nip) {
            $user = User::create([
                'name'     => $nama,
                'email'    => $email,
                'password' => Hash::make($password),
            ]);

            $user->assignRole(RoleUser::GURU->value);

            Guru::create([
                'user_id' => $user->id,
                'nip'     => $nip ?: null,
            ]);
        });

        $this->newLine();
        $this->info("✓ Akun guru '{$nama}' ({$email}) berhasil dibuat.");
        return self::SUCCESS;
    }
}
