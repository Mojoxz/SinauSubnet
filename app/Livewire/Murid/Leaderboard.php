<?php

declare(strict_types=1);

namespace App\Livewire\Murid;

use App\Models\Murid;
use Livewire\Component;
use Livewire\WithPagination;

class Leaderboard extends Component
{
    use WithPagination;
    
    // Pagination reset theme to Tailwind since Livewire 3 defaults to Tailwind
    
    public function render()
    {
        $murid = auth()->user()->murid;
        
        // Papan Peringkat: diurutkan total_poin menurun, lalu poin_dicapai_pada menaik (tie-breaker kecepatan capai poin)
        $daftarPeringkat = Murid::with('user')
            ->orderBy('total_poin', 'desc')
            ->orderBy('poin_dicapai_pada', 'asc')
            ->orderBy('id', 'asc') // Fallback deterministik
            ->paginate(15);
            
        // Cari peringkat milik user yang sedang login tanpa memuat semua data ke memori
        // Query untuk mencari berapa banyak murid yang total_poin > murid ini
        // Atau (total_poin = murid ini AND poin_dicapai_pada < murid ini)
        // Atau (total_poin = murid ini AND poin_dicapai_pada = murid ini AND id < murid ini)
        
        $peringkatSaya = Murid::where('total_poin', '>', $murid->total_poin)
            ->orWhere(function ($query) use ($murid) {
                if ($murid->poin_dicapai_pada !== null) {
                    $query->where('total_poin', $murid->total_poin)
                          ->where('poin_dicapai_pada', '<', $murid->poin_dicapai_pada);
                } else {
                    $query->where('total_poin', $murid->total_poin)
                          ->whereNotNull('poin_dicapai_pada');
                }
            })
            ->orWhere(function ($query) use ($murid) {
                if ($murid->poin_dicapai_pada !== null) {
                    $query->where('total_poin', $murid->total_poin)
                          ->where('poin_dicapai_pada', '=', $murid->poin_dicapai_pada)
                          ->where('id', '<', $murid->id);
                } else {
                    $query->where('total_poin', $murid->total_poin)
                          ->whereNull('poin_dicapai_pada')
                          ->where('id', '<', $murid->id);
                }
            })
            ->count() + 1; // + 1 karena peringkat dimulai dari 1
            
        return view('livewire.murid.leaderboard.index', [
            'daftarPeringkat' => $daftarPeringkat,
            'peringkatSaya' => $peringkatSaya,
            'muridSaya' => $murid
        ])->layout('layouts.app', ['title' => 'Papan Peringkat (Leaderboard)']);
    }
}
