<?php

declare(strict_types=1);

namespace App\Livewire\Murid\Materi;

use App\Models\Materi;
use App\Services\MarkdownSanitizer;
use App\Actions\UpdateProgressPblAction;
use Livewire\Component;

class Baca extends Component
{
    public Materi $materi;
    public string $htmlKonten;
    public bool $sudahSelesai = false;

    public function mount(Materi $materi, MarkdownSanitizer $sanitizer)
    {
        $this->authorize('view', $materi);

        $murid = auth()->user()->murid;
        if ($materi->level > $murid->getCurrentPblLevel()) {
            abort(403, 'Anda belum membuka level pembelajaran ini.');
        }

        $this->materi = $materi;
        $this->htmlKonten = $sanitizer->sanitizeAndConvert($materi->konten);

        $statusTahap = $murid->getPblStateForMateri($materi->id);
        if ($statusTahap !== 'materi') {
            $this->sudahSelesai = true;
        }
    }

    public function selesaikanMateri(UpdateProgressPblAction $action)
    {
        $murid = auth()->user()->murid;
        
        if ($murid->getPblStateForMateri($this->materi->id) === 'materi') {
            $action->execute($murid, $this->materi, 'materi', 100);
            $this->sudahSelesai = true;
            session()->flash('success', 'Selamat! Modul Praktikum telah terbuka.');
        }
    }

    public function render()
    {
        return view('livewire.murid.materi.baca')
            ->layout('layouts.app', ['title' => $this->materi->judul]);
    }
}
