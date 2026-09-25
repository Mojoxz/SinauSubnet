<?php

declare(strict_types=1);

namespace App\Livewire\Murid\Materi;

use App\Models\Materi;
use App\Models\Progress;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $muridId = auth()->user()->murid->id;
        
        $semuaMateri = Materi::where('is_aktif', true)
                             ->orderBy('level')
                             ->orderBy('urutan')
                             ->get();
                             
        $materiPerLevel = $semuaMateri->groupBy('level');
        $progress = Progress::where('murid_id', $muridId)->get();
        
        $unlockedLevel = 1;
        foreach($materiPerLevel as $lvl => $materis) {
            $semuaSelesai = true;
            foreach($materis as $m) {
                $p = $progress->where('materi_id', $m->id)->first();
                if (!$p || $p->persen_quiz < 100) {
                    $semuaSelesai = false;
                    break;
                }
            }
            if ($semuaSelesai) {
                $unlockedLevel = $lvl + 1;
            } else {
                break;
            }
        }

        return view('livewire.murid.materi.index', [
            'materiPerLevel' => $materiPerLevel,
            'levelTertinggiMurid' => $unlockedLevel
        ])->layout('layouts.app', ['title' => 'Materi Belajar']);
    }
}
