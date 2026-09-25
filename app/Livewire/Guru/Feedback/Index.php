<?php

declare(strict_types=1);

namespace App\Livewire\Guru\Feedback;

use App\Models\Feedback;
use App\Models\Murid;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $isi = '';
    public $murid_id = '';
    
    // Untuk edit
    public $feedback_id = null;
    public $modeForm = false;

    protected $rules = [
        'murid_id' => 'required|exists:murid,id',
        'isi' => 'required|string|min:10',
    ];

    public function render()
    {
        $feedbacks = Feedback::with(['murid.user'])
            ->where('guru_id', auth()->user()->guru->id)
            ->latest()
            ->paginate(10);
            
        $daftarMurid = Murid::with('user')->get();

        return view('livewire.guru.feedback.index', [
            'feedbacks' => $feedbacks,
            'daftarMurid' => $daftarMurid,
        ])->layout('layouts.app', ['title' => 'Manajemen Umpan Balik (Feedback)']);
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->feedback_id = null;
        $this->isi = '';
        $this->murid_id = '';
        $this->modeForm = false;
    }

    public function bukaForm()
    {
        $this->resetForm();
        $this->modeForm = true;
    }

    public function editFeedback($id)
    {
        $feedback = Feedback::findOrFail($id);
        $this->authorize('update', $feedback);
        
        $this->feedback_id = $feedback->id;
        $this->murid_id = $feedback->murid_id;
        $this->isi = $feedback->isi;
        $this->modeForm = true;
    }

    public function simpan()
    {
        $this->validate();

        if ($this->feedback_id) {
            $feedback = Feedback::findOrFail($this->feedback_id);
            $this->authorize('update', $feedback);
            $feedback->update([
                'murid_id' => $this->murid_id,
                'isi' => $this->isi,
            ]);
            session()->flash('success', 'Feedback berhasil diperbarui.');
        } else {
            Feedback::create([
                'guru_id' => auth()->user()->guru->id,
                'murid_id' => $this->murid_id,
                'isi' => $this->isi,
            ]);
            session()->flash('success', 'Feedback berhasil dikirim ke murid.');
        }

        $this->resetForm();
    }
    
    public function hapusFeedback($id)
    {
        $feedback = Feedback::findOrFail($id);
        $this->authorize('delete', $feedback);
        $feedback->delete();
        
        session()->flash('success', 'Feedback berhasil dihapus.');
    }
}
