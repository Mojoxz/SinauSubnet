<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ButuhKoreksiManualNotification extends Notification implements ShouldQueue
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
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $labelTipe = $this->tipe === 'quiz' ? 'Kuis' : 'Praktikum';
        
        return [
            'title' => "Perlu Koreksi Manual Guru",
            'message' => "Sistem AI gagal memberikan penilaian untuk sebuah jawaban uraian pada {$labelTipe}. Mohon berikan penilaian manual.",
            'action_url' => '/guru/penilaian/uraian', // Arahkan ke panel review guru
            'icon' => 'exclamation-triangle',
            'type' => $this->tipe,
            'jawaban_id' => $this->jawabanId,
        ];
    }
}
