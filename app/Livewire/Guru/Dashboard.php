<?php

declare(strict_types=1);

namespace App\Livewire\Guru;

use App\Models\Murid;
use App\Models\JawabanQuiz;
use App\Models\JawabanPraktikum;
use App\Models\PenilaianPraktikum;
use App\Services\Penilaian\NilaiCalculator;
use App\Services\Penilaian\PsikomotorikCalculator;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(NilaiCalculator $nilaiCalc, PsikomotorikCalculator $psikoCalc)
    {
        $totalMurid = Murid::count();
        
        $butuhManualQuiz = JawabanQuiz::whereIn('status_penilaian', ['perlu_manual'])->count();
        $butuhManualPrak = JawabanPraktikum::whereIn('status_penilaian', ['perlu_manual'])->count();
        $totalAntreanReview = $butuhManualQuiz + $butuhManualPrak;

        // Praktikum belum dinilai guru secara psikomotorik (hanya jika murid sudah upload file tapi belum dinilai guru)
        $belumDinilaiPsiko = \App\Models\JawabanPraktikum::whereNotNull('file_bukti_path')
            ->whereNotIn('murid_id', function($q) {
                $q->select('murid_id')->from('penilaian_praktikum');
            })
            ->distinct('murid_id', 'soal_praktikum_id')
            ->count();

        // Rata-rata kelas 
        $murids = Murid::all();
        $totalKognitif = 0;
        $totalPsiko = 0;
        $c3_total = 0; $c4_total = 0; $c5_total = 0;
        
        foreach ($murids as $m) {
            $totalKognitif += $nilaiCalc->hitungRataRataKognitif($m->id);
            $totalPsiko += $psikoCalc->hitungRataRata($m->id);
            
            $c3_total += $nilaiCalc->hitungNilaiBloom($m->id, 'C3');
            $c4_total += $nilaiCalc->hitungNilaiBloom($m->id, 'C4');
            $c5_total += $nilaiCalc->hitungNilaiBloom($m->id, 'C5');
        }
        
        $rataKognitifKelas = $totalMurid > 0 ? round($totalKognitif / $totalMurid, 2) : 0;
        $rataPsikoKelas = $totalMurid > 0 ? round($totalPsiko / $totalMurid, 2) : 0;
        
        $rataC3 = $totalMurid > 0 ? round($c3_total / $totalMurid, 2) : 0;
        $rataC4 = $totalMurid > 0 ? round($c4_total / $totalMurid, 2) : 0;
        $rataC5 = $totalMurid > 0 ? round($c5_total / $totalMurid, 2) : 0;

        return view('livewire.guru.dashboard', [
            'totalMurid' => $totalMurid,
            'totalAntreanReview' => $totalAntreanReview,
            'belumDinilaiPsiko' => $belumDinilaiPsiko,
            'rataKognitifKelas' => $rataKognitifKelas,
            'chartData' => json_encode([
                'labels' => ['Kognitif C3', 'Kognitif C4', 'Kognitif C5', 'Psikomotorik'],
                'data' => [$rataC3, $rataC4, $rataC5, $rataPsikoKelas]
            ])
        ])->layout('layouts.app', ['title' => 'Dashboard Guru']);
    }
}
