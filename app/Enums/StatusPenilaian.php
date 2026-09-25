<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusPenilaian: string
{
    case FINAL_OTOMATIS   = 'final';
    case MENUNGGU_AI      = 'menunggu_ai';
    case DINILAI_AI       = 'dinilai_ai';
    case DIVALIDASI_GURU  = 'divalidasi_guru';
    case DIKOREKSI_GURU   = 'dikoreksi_guru';
    case PERLU_MANUAL     = 'perlu_manual';

    /**
     * Kembalikan class Tailwind CSS untuk komponen <x-domain.status-badge>.
     * Didefinisikan satu kali di sini, dipakai konsisten di seluruh UI dan PDF.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::FINAL_OTOMATIS  => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            self::MENUNGGU_AI     => 'bg-amber-100 text-amber-800 border-amber-300',
            self::DINILAI_AI      => 'bg-blue-100 text-blue-800 border-blue-300',
            self::DIVALIDASI_GURU => 'bg-teal-100 text-teal-800 border-teal-300',
            self::DIKOREKSI_GURU  => 'bg-purple-100 text-purple-800 border-purple-300',
            self::PERLU_MANUAL    => 'bg-rose-100 text-rose-800 border-rose-300',
        };
    }

    /**
     * Kembalikan kode warna Hex untuk ekspor PDF via dompdf.
     */
    public function colorHex(): string
    {
        return match ($this) {
            self::FINAL_OTOMATIS  => '#059669',
            self::MENUNGGU_AI     => '#D97706',
            self::DINILAI_AI      => '#2563EB',
            self::DIVALIDASI_GURU => '#0D9488',
            self::DIKOREKSI_GURU  => '#7C3AED',
            self::PERLU_MANUAL    => '#E11D48',
        };
    }

    /**
     * Kembalikan label Bahasa Indonesia untuk ditampilkan di UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::FINAL_OTOMATIS  => 'Dinilai Otomatis',
            self::MENUNGGU_AI     => 'Menunggu AI',
            self::DINILAI_AI      => 'Dinilai AI',
            self::DIVALIDASI_GURU => 'Divalidasi Guru',
            self::DIKOREKSI_GURU  => 'Dikoreksi Guru',
            self::PERLU_MANUAL    => 'Perlu Koreksi Manual',
        };
    }

    /**
     * Apakah status ini masih memerlukan tindakan dari guru?
     */
    public function perluTindakanGuru(): bool
    {
        return in_array($this, [self::PERLU_MANUAL, self::DINILAI_AI]);
    }

    /**
     * Apakah status ini berasal dari alur penilaian AI?
     */
    public function dariAi(): bool
    {
        return in_array($this, [
            self::MENUNGGU_AI,
            self::DINILAI_AI,
            self::DIVALIDASI_GURU,
            self::DIKOREKSI_GURU,
            self::PERLU_MANUAL,
        ]);
    }
}
