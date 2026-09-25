<?php

declare(strict_types=1);

namespace App\Livewire\Murid;

use App\Models\Badge;
use Livewire\Component;

class BadgeKoleksi extends Component
{
    public function render()
    {
        $murid = auth()->user()->murid;
        
        // Ambil ID badge yang sudah didapatkan
        $badgeDidapatIds = $murid->badges()->pluck('badge.id')->toArray();
        
        $semuaBadge = Badge::orderBy('syarat_poin', 'asc')->get();
        
        // Klasifikasikan mana yang sudah didapat dan mana yang masih terkunci
        $badgesDidapat = [];
        $badgesTerkunci = [];
        
        foreach ($semuaBadge as $badge) {
            if (in_array($badge->id, $badgeDidapatIds)) {
                // Attach tanggal didapat untuk UI
                $pivot = $murid->badges()->where('badge_id', $badge->id)->first();
                $badge->diperoleh_pada = $pivot->pivot->diperoleh_pada ?? null;
                $badgesDidapat[] = $badge;
            } else {
                $badgesTerkunci[] = $badge;
            }
        }
        
        return view('livewire.murid.badge.index', [
            'badgesDidapat' => collect($badgesDidapat),
            'badgesTerkunci' => collect($badgesTerkunci),
            'totalPoin' => $murid->total_poin
        ])->layout('layouts.app', ['title' => 'Koleksi Lencana (Badges)']);
    }
}
