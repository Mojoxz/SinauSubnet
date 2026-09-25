<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JawabanUraianDinilaiNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $tipe;
    protected $jawabanId;

    public function __construct(string $tipe, int $jawabanId)
    {
        $this->tipe = $tipe; // 'quiz' or 'praktikum'
        $this->jawabanId = $jawabanId;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Hanya simpan di database untuk In-App Notification
    }

    public function toDatabase(object $notifiable): array
    {
        $labelTipe = $this->tipe === 'quiz' ? 'Kuis' : 'Praktikum';
        
        return [
            'title' => "Hasil Uraian {$labelTipe} Dinilai",
            'message' => "Jawaban uraian Anda pada {$labelTipe} telah selesai dikoreksi secara otomatis. Silakan cek detail nilainya.",
            'action_url' => '/murid/nilai', // Arahkan ke halaman nilai
            'icon' => 'check-badge',
            'type' => $this->tipe,
            'jawaban_id' => $this->jawabanId,
        ];
    }
}
