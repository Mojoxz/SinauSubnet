<?php

declare(strict_types=1);

namespace App\Livewire\Guru\Quiz;

use App\Models\Materi;
use App\Models\Quiz;
use Livewire\Component;

class Buat extends Component
{
    public $materi_id = '';
    public $judul = '';
    public $deskripsi = '';
    public $durasi_menit = 30;
    public $kkm = 75;
    public $bonus_kecepatan_maks = 50;
    public $is_aktif = true;
    
    public $daftarMateri = [];

    protected $rules = [
        'materi_id' => 'required|exists:materi,id',
        'judul' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'durasi_menit' => 'required|integer|min:5|max:180',
        'kkm' => 'required|integer|min:1|max:100',
        'bonus_kecepatan_maks' => 'required|integer|min:0|max:1000',
    ];

    public function mount()
    {
        $this->authorize('create', Quiz::class);
        
        $this->daftarMateri = Materi::where('guru_id', auth()->user()->guru->id)
            ->orderBy('level')
            ->orderBy('urutan')
            ->get();
            
        if ($this->daftarMateri->isNotEmpty()) {
            $this->materi_id = $this->daftarMateri->first()->id;
        }
    }

    public function simpan()
    {
        $this->validate();

        $quiz = Quiz::create([
            'materi_id' => $this->materi_id,
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'durasi_menit' => $this->durasi_menit,
            'kkm' => $this->kkm,
            'bonus_kecepatan_maks' => $this->bonus_kecepatan_maks,
            'is_aktif' => $this->is_aktif,
        ]);

        session()->flash('success', 'Quiz berhasil ditambahkan. Silakan lengkapi soal-soalnya.');
        return $this->redirectRoute('guru.quiz.detail', ['quiz' => $quiz->id], navigate: true);
    }

    public function render()
    {
        return view('livewire.guru.quiz.buat')
            ->layout('layouts.app', ['title' => 'Buat Quiz Baru']);
    }
}
