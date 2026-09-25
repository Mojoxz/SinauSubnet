<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use Livewire\Component;

class Tentang extends Component
{
    public function render()
    {
        return view('livewire.public.tentang')
            ->layout('layouts.public', ['title' => 'Tentang & Fitur']);
    }
}
