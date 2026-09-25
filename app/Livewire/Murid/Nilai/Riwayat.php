<?php

declare(strict_types=1);

namespace App\Livewire\Murid\Nilai;

use App\Models\HasilQuiz;
use App\Models\PenilaianPraktikum;
use App\Services\Penilaian\NilaiCalculator;
use App\Services\Penilaian\PsikomotorikCalculator;
use Livewire\Component;
use Livewire\WithPagination;

class Riwayat extends Component
{
    use WithPagination;

    public $filterTipe = 'semua'; // semua, quiz, praktikum

    public function render(NilaiCalculator $nilaiCalc, PsikomotorikCalculator $psikoCalc)
    {
        $muridId = auth()->user()->murid->id;
        
        // Data Riwayat Evaluasi (Quiz & Praktikum)
        $riwayatQuiz = HasilQuiz::with('quiz.materi')->where('murid_id', $muridId)->latest('waktu_selesai')->get();
        $riwayatPrak = PenilaianPraktikum::with(['praktikum.materi', 'guru.user'])->where('murid_id', $muridId)->latest('updated_at')->get();
        
        $koleksi = collect();
        if ($this->filterTipe === 'semua' || $this->filterTipe === 'quiz') {
            foreach ($riwayatQuiz as $q) {
                $q->jenis = 'quiz';
                $q->tanggal = $q->waktu_selesai;
                $koleksi->push($q);
            }
        }
        
        if ($this->filterTipe === 'semua' || $this->filterTipe === 'praktikum') {
            foreach ($riwayatPrak as $p) {
                $p->jenis = 'praktikum';
                $p->tanggal = $p->updated_at;
                $koleksi->push($p);
            }
        }
        
        $riwayat = $koleksi->sortByDesc('tanggal')->values();

        // Data untuk Radar Chart
        $c3 = $nilaiCalc->hitungNilaiBloom($muridId, 'C3');
        $c4 = $nilaiCalc->hitungNilaiBloom($muridId, 'C4');
        $c5 = $nilaiCalc->hitungNilaiBloom($muridId, 'C5');
        $psiko = $psikoCalc->hitungRataRata($muridId);
        
        return view('livewire.murid.nilai.riwayat', [
            'riwayat' => $riwayat,
            'chartData' => json_encode([
                'labels' => ['Kognitif C3 (Penerapan)', 'Kognitif C4 (Analisis)', 'Kognitif C5 (Evaluasi)', 'Kinerja Praktik (Psiko)'],
                'data' => [$c3, $c4, $c5, $psiko]
            ])
        ])->layout('layouts.app', ['title' => 'Riwayat Nilai & Progress']);
    }
}
