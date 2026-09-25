<?php

declare(strict_types=1);

namespace App\Livewire\Guru\Materi;

use App\Models\Materi;
use App\Services\MarkdownSanitizer;
use Livewire\Component;

class Detail extends Component
{
    public Materi $materi;
    public string $htmlKonten;

    public function mount(Materi $materi, MarkdownSanitizer $sanitizer)
    {
        $this->authorize('view', $materi);
        
        $this->materi = $materi;
        // Parse raw markdown ke HTML aman untuk preview guru
        $this->htmlKonten = $sanitizer->sanitizeAndConvert($materi->konten);
    }

    public function render()
    {
        return view('livewire.guru.materi.detail')
            ->layout('layouts.app', ['title' => 'Detail Materi']);
    }
}
