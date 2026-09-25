<?php

declare(strict_types=1);

namespace App\Livewire\Murid\Quiz;

use App\Models\HasilQuiz;
use App\Models\Quiz;
use Livewire\Component;

class Mulai extends Component
{
    public Quiz $quiz;
    public $hasilKuisAda;

    public function mount(Quiz $quiz)
    {
        $murid = auth()->user()->murid;

        if ($quiz->materi->level > $murid->getCurrentPblLevel()) {
            abort(403, 'Anda belum membuka level pembelajaran ini.');
        }

        $this->quiz = $quiz;
        
        // Cek percobaan Kuis
        $this->hasilKuisAda = HasilQuiz::where('quiz_id', $quiz->id)
            ->where('murid_id', $murid->id)
            ->first();

        if ($this->hasilKuisAda && $this->hasilKuisAda->waktu_selesai) {
            // Jika sudah selesai, redirect ke halaman hasil
            return redirect()->route('murid.quiz.hasil', $this->hasilKuisAda->id);
        }
    }

    public function mulaiUjian()
    {
        $murid = auth()->user()->murid;

        // Cek lagi untuk menghindari double submit
        $hasil = HasilQuiz::where('quiz_id', $this->quiz->id)
            ->where('murid_id', $murid->id)
            ->first();

        if (!$hasil) {
            // Percobaan Pertama: Buat HasilQuiz baru dengan waktu_mulai server side
            $hasil = HasilQuiz::create([
                'quiz_id' => $this->quiz->id,
                'murid_id' => $murid->id,
                'waktu_mulai' => now(), // Catat waktu Server
                'status' => \App\Enums\StatusPenilaian::MENUNGGU_AI,
            ]);
        }

        return redirect()->route('murid.quiz.kerjakan', $this->quiz->id);
    }

    public function render()
    {
        return view('livewire.murid.quiz.mulai')
            ->layout('layouts.app', ['title' => 'Mulai Quiz Evaluasi']);
    }
}
