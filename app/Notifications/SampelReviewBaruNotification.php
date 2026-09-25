<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SampelReviewBaruNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $tipe;
    protected $jawabanId;

    public function __construct(string $tipe, int $jawabanId)
    {
        $this->tipe = $tipe;
        $this->jawabanId = $jawabanId;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => "Sampel Review Uji Validitas Baru",
            'message' => "Sistem telah memilih 1 jawaban uraian baru secara acak (15%) sebagai sampel review permanen untuk uji validitas instrumen penelitian.",
            'action_url' => '/guru/penilaian/uraian',
            'icon' => 'beaker',
            'type' => $this->tipe,
            'jawaban_id' => $this->jawabanId,
        ];
    }
}
