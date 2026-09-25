<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use Livewire\Component;

class Beranda extends Component
{
    public function render()
    {
        return view('livewire.public.beranda')
            ->layout('layouts.public', ['title' => 'Beranda']);
    }
}
