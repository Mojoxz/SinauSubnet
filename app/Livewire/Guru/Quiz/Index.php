<?php

declare(strict_types=1);

namespace App\Livewire\Guru\Quiz;

use App\Models\Materi;
use App\Models\Quiz;
use Livewire\Component;

class Index extends Component
{
    public $quizzes;

    public function mount()
    {
        $this->loadQuizzes();
    }

    public function loadQuizzes()
    {
        $this->quizzes = Quiz::with(['materi' => function($query) {
            $query->select('id', 'judul', 'level');
        }])
        ->whereHas('materi', function($query) {
            $query->where('guru_id', auth()->user()->guru->id);
        })
        ->orderBy(
            \App\Models\Materi::select('level')
                ->whereColumn('quiz.materi_id', 'materi.id')
        )
        ->orderBy('id', 'desc')
        ->get();
    }

    public function deleteQuiz($id)
    {
        $quiz = Quiz::findOrFail($id);
        
        $this->authorize('delete', $quiz);

        if ($quiz->memilikiJawabanMurid()) {
            // SOFT ARCHIVE
            $quiz->update(['is_aktif' => false]);
            $quiz->soalQuizzes()->update(['is_aktif' => false]);
            
            session()->flash('warning', 'Quiz tidak dapat dihapus permanen karena murid telah mengerjakannya. Data diarsipkan secara otomatis.');
        } else {
            // HARD DELETE
            $quiz->soalQuizzes()->delete();
            $quiz->delete();
            
            session()->flash('success', 'Modul Quiz berhasil dihapus permanen.');
        }

        $this->loadQuizzes();
    }

    public function render()
    {
        return view('livewire.guru.quiz.index')
            ->layout('layouts.app', ['title' => 'Manajemen Evaluasi Quiz']);
    }
}
