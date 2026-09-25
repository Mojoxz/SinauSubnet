<?php

declare(strict_types=1);

namespace App\Livewire\Murid\Feedback;

use App\Models\Feedback;
use Livewire\Component;
use Livewire\WithPagination;

class Daftar extends Component
{
    use WithPagination;

    public function render()
    {
        $feedbacks = Feedback::with(['guru.user'])
            ->where('murid_id', auth()->user()->murid->id)
            ->latest()
            ->paginate(10);

        return view('livewire.murid.feedback.daftar', [
            'feedbacks' => $feedbacks,
        ])->layout('layouts.app', ['title' => 'Umpan Balik Guru (Feedback)']);
    }

    public function tandaiDibaca($id)
    {
        $feedback = Feedback::findOrFail($id);
        $this->authorize('view', $feedback);

        if (is_null($feedback->dibaca_pada)) {
            $feedback->update(['dibaca_pada' => now()]);
        }
    }
}
