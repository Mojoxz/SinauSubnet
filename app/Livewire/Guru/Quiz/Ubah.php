<?php

declare(strict_types=1);

namespace App\Livewire\Guru\Quiz;

use App\Models\Materi;
use App\Models\Quiz;
use Livewire\Component;

class Ubah extends Component
{
    public Quiz $quiz;

    public $materi_id;
    public $judul;
    public $deskripsi;
    public $durasi_menit;
    public $kkm;
    public $bonus_kecepatan_maks;
    public $is_aktif;
    
    public $daftarMateri = [];

    protected $rules = [
        'materi_id' => 'required|exists:materi,id',
        'judul' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'durasi_menit' => 'required|integer|min:5|max:180',
        'kkm' => 'required|integer|min:1|max:100',
        'bonus_kecepatan_maks' => 'required|integer|min:0|max:1000',
        'is_aktif' => 'boolean',
    ];

    public function mount(Quiz $quiz)
    {
        $this->authorize('update', $quiz);

        $this->quiz = $quiz;
        $this->materi_id = $quiz->materi_id;
        $this->judul = $quiz->judul;
        $this->deskripsi = $quiz->deskripsi;
        $this->durasi_menit = $quiz->durasi_menit;
        $this->kkm = $quiz->kkm;
        $this->bonus_kecepatan_maks = $quiz->bonus_kecepatan_maks;
        $this->is_aktif = $quiz->is_aktif;

        $this->daftarMateri = Materi::where('guru_id', auth()->user()->guru->id)
            ->orderBy('level')
            ->orderBy('urutan')
            ->get();
    }

    public function simpan()
    {
        $this->validate();

        $this->quiz->update([
            'materi_id' => $this->materi_id,
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'durasi_menit' => $this->durasi_menit,
            'kkm' => $this->kkm,
            'bonus_kecepatan_maks' => $this->bonus_kecepatan_maks,
            'is_aktif' => $this->is_aktif,
        ]);

        session()->flash('success', 'Pengaturan Kuis berhasil diperbarui.');
        return $this->redirectRoute('guru.quiz.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.guru.quiz.ubah')
            ->layout('layouts.app', ['title' => 'Ubah Quiz']);
    }
}
