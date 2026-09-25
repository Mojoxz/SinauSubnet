<?php

declare(strict_types=1);

namespace App\Livewire\Murid\Nilai;

use App\Models\HasilQuiz;
use Livewire\Component;

class DetailHasilQuiz extends Component
{
    public HasilQuiz $hasilQuiz;
    public $jawabanList;

    public function mount(HasilQuiz $hasilQuiz)
    {
        $this->authorize('view', $hasilQuiz);
        
        $this->hasilQuiz = $hasilQuiz->load(['quiz.materi', 'quiz.soalQuizzes']);
        
        $this->jawabanList = $this->hasilQuiz->jawabanQuizzes()
            ->with('soalQuiz')
            ->get()
            ->keyBy('soal_quiz_id');
    }

    public function render()
    {
        return view('livewire.murid.nilai.detail-hasil-quiz')
            ->layout('layouts.app', ['title' => 'Detail Hasil Ujian']);
    }
}
