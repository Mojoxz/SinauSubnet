<?php

declare(strict_types=1);

namespace App\Livewire\Murid;

use App\Models\Materi;
use App\Models\Progress;
use App\Services\Penilaian\NilaiCalculator;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(NilaiCalculator $calculator)
    {
        $murid = auth()->user()->murid;

        // Metrik Statistik
        $totalPoin = $murid->total_poin;
        $badgeTerbaru = $murid->badges()->latest('murid_badge.diperoleh_pada')->first();
        
        $progress = Progress::where('murid_id', $murid->id)->get();
        // Materi selesai: Persen quiz = 100
        $materiSelesai = $progress->where('persen_quiz', 100)->count();
        
        $skorRataRata = $calculator->hitungRataRataKognitif($murid->id);

        // Rekomendasi Materi
        $materiAktif = Materi::where('is_aktif', true)->orderBy('level')->orderBy('urutan')->get();
        
        // Buat mapping pbl status
        $statusPblArray = [];
        $rekomendasiMateri = null;

        // Hitung level maksimum yang terbuka
        $unlockedLevel = 1;
        $levelSelesai = true;
        
        // Kita kelompokkan materi per level
        $materiPerLevel = $materiAktif->groupBy('level');
        foreach($materiPerLevel as $lvl => $materis) {
            $semuaSelesaiDiLevelIni = true;
            foreach($materis as $m) {
                // Periksa progress untuk materi ini
                $p = $progress->where('materi_id', $m->id)->first();
                if (!$p || $p->persen_quiz < 100) {
                    $semuaSelesaiDiLevelIni = false;
                    break;
                }
            }
            if ($semuaSelesaiDiLevelIni) {
                $unlockedLevel = $lvl + 1;
            } else {
                break; // Stop checking further levels
            }
        }

        foreach ($materiAktif as $materi) {
            $prog = $progress->where('materi_id', $materi->id)->first();
            $persen = 0;
            $statusStr = 'belum';
            
            $statusTahap = 'materi';
            
            if ($prog) {
                $totalLangkah = 0;
                if ($prog->persen_materi == 100) {
                    $totalLangkah++;
                    $statusTahap = 'praktikum';
                }
                if ($prog->persen_praktikum == 100) {
                    $totalLangkah++;
                    $statusTahap = 'quiz';
                }
                if ($prog->persen_quiz == 100) {
                    $totalLangkah++;
                    $statusTahap = 'selesai';
                }
                
                $persen = round(($totalLangkah / 3) * 100);
                
                if ($totalLangkah == 3) {
                    $statusStr = 'selesai';
                } elseif ($totalLangkah > 0) {
                    $statusStr = 'sedang';
                }
            }
            
            $statusPblArray[$materi->id] = [
                'persen' => $persen,
                'status' => $statusStr,
                'isLocked' => $materi->level > $unlockedLevel
            ];

            // Cari rekomendasi pertama yang belum selesai (is_aktif = true & level sesuai)
            if ($rekomendasiMateri === null && $statusStr !== 'selesai' && $materi->level <= $unlockedLevel) {
                $rekomendasiMateri = collect([
                    'materi' => $materi,
                    'statusPbl' => $statusTahap // 'materi', 'praktikum', 'quiz'
                ]);
            }
        }

        return view('livewire.murid.dashboard', [
            'totalPoin' => $totalPoin,
            'badgeTerbaru' => $badgeTerbaru,
            'materiSelesai' => $materiSelesai,
            'skorRataRata' => $skorRataRata,
            'materiAktif' => $materiAktif,
            'statusPblArray' => $statusPblArray,
            'rekomendasiMateri' => $rekomendasiMateri
        ])->layout('layouts.app', ['title' => 'Dashboard Siswa']);
    }
}
