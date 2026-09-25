<?php

declare(strict_types=1);

namespace App\Livewire\Murid\Nilai;

use App\Models\PenilaianPraktikum;
use Livewire\Component;

class DetailPraktikum extends Component
{
    public PenilaianPraktikum $penilaianPraktikum;
    public $jawabanList;

    public function mount(PenilaianPraktikum $penilaianPraktikum)
    {
        $this->authorize('view', $penilaianPraktikum);
        
        $this->penilaianPraktikum = $penilaianPraktikum->load([
            'praktikum.materi', 
            'praktikum.soalPraktikums',
            'nilaiAspeks.rubrikPraktikum',
            'guru.user'
        ]);
        
        // Ambil jawaban praktikum dari murid untuk praktikum ini
        $this->jawabanList = \App\Models\JawabanPraktikum::where('murid_id', auth()->user()->murid->id)
            ->whereIn('soal_praktikum_id', $this->penilaianPraktikum->praktikum->soalPraktikums->pluck('id'))
            ->get()
            ->keyBy('soal_praktikum_id');
    }

    public function render()
    {
        return view('livewire.murid.nilai.detail-praktikum')
            ->layout('layouts.app', ['title' => 'Detail Hasil Praktikum']);
    }
}
