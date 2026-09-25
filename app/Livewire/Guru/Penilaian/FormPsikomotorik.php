<?php

declare(strict_types=1);

namespace App\Livewire\Guru\Penilaian;

use App\Models\Praktikum;
use App\Models\Murid;
use App\Models\PenilaianPraktikum;
use App\Models\NilaiAspek;
use App\Models\RubrikPraktikum;
use App\Services\Penilaian\PsikomotorikCalculator;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class FormPsikomotorik extends Component
{
    public Praktikum $praktikum;
    public Murid $murid;
    public $penilaianId;
    
    // Form State
    public $catatan = '';
    public array $skorAspek = []; // [rubrik_id => skor]
    public $rubrikList = [];

    // State display
    public $jawabanMuridList = [];

    public function mount(Praktikum $praktikum, Murid $murid)
    {
        $this->authorize('create', PenilaianPraktikum::class);
        
        $this->praktikum = $praktikum;
        $this->murid = $murid;
        
        $this->rubrikList = RubrikPraktikum::where('praktikum_id', $praktikum->id)
            ->orderBy('urutan')
            ->get();
            
        // Init skor
        foreach ($this->rubrikList as $rubrik) {
            $this->skorAspek[$rubrik->id] = 1; // Default minimum 1
        }
        
        // Cek apakah sudah pernah dinilai
        $penilaian = PenilaianPraktikum::with('nilaiAspeks')
            ->where('praktikum_id', $praktikum->id)
            ->where('murid_id', $murid->id)
            ->first();
            
        if ($penilaian) {
            $this->penilaianId = $penilaian->id;
            $this->catatan = $penilaian->catatan;
            foreach ($penilaian->nilaiAspeks as $nilai) {
                $this->skorAspek[$nilai->rubrik_praktikum_id] = $nilai->skor;
            }
        }
        
        // Ambil bukti upload murid (jawaban yang punya file_bukti_path)
        $this->jawabanMuridList = \App\Models\JawabanPraktikum::with('soalPraktikum')
            ->where('murid_id', $murid->id)
            ->whereHas('soalPraktikum', function($q) use ($praktikum) {
                $q->where('praktikum_id', $praktikum->id);
            })
            ->whereNotNull('file_bukti_path')
            ->get();
    }

    public function simpan(PsikomotorikCalculator $kalkulator)
    {
        $this->validate([
            'catatan' => 'nullable|string',
            'skorAspek.*' => 'required|integer|min:1|max:4',
        ]);

        DB::transaction(function () {
            $penilaian = PenilaianPraktikum::updateOrCreate(
                [
                    'praktikum_id' => $this->praktikum->id,
                    'murid_id' => $this->murid->id,
                ],
                [
                    'guru_id' => auth()->user()->guru->id,
                    'catatan' => $this->catatan,
                ]
            );

            foreach ($this->skorAspek as $rubrikId => $skor) {
                NilaiAspek::updateOrCreate(
                    [
                        'penilaian_praktikum_id' => $penilaian->id,
                        'rubrik_praktikum_id' => $rubrikId,
                    ],
                    [
                        'skor' => $skor,
                    ]
                );
            }
            
            $this->penilaianId = $penilaian->id;
        });

        // Hitung nilai akhir untuk ditampilkan
        $nilai = $kalkulator->hitung($this->penilaianId);

        session()->flash('success', "Penilaian Psikomotorik berhasil disimpan. Nilai Kinerja Praktik: {$nilai}%");
    }

    public function render()
    {
        return view('livewire.guru.penilaian.form-psikomotorik')
            ->layout('layouts.app', ['title' => 'Form Penilaian Praktikum (Psikomotorik)']);
    }
}
