<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

class NavigationNotificationBell extends Component
{
    public int $unreadCount = 0;

    // TODO: Implementasi logika get notifikasi dari database saat masuk Tahap 5 (Notifikasi)

    public function render()
    {
        return view('livewire.navigation-notification-bell');
    }
}
