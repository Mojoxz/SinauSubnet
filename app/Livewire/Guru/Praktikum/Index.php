<?php

declare(strict_types=1);

namespace App\Livewire\Guru\Praktikum;

use App\Models\Praktikum;
use Livewire\Component;

class Index extends Component
{
    public $praktikums;

    public function mount()
    {
        $this->loadPraktikum();
    }

    public function loadPraktikum()
    {
        // Ambil praktikum yang terhubung dengan materi milik guru yang login
        $this->praktikums = Praktikum::with(['materi' => function($query) {
            $query->select('id', 'judul', 'level');
        }])
        ->whereHas('materi', function($query) {
            $query->where('guru_id', auth()->user()->guru->id);
        })
        ->orderBy(
            \App\Models\Materi::select('level')
                ->whereColumn('praktikum.materi_id', 'materi.id')
        )
        ->orderBy('id', 'desc')
        ->get();
    }

    public function deletePraktikum($id)
    {
        $praktikum = Praktikum::findOrFail($id);
        
        $this->authorize('delete', $praktikum);

        // Cek Soft-Archive Logika Berjenjang
        if ($praktikum->memilikiJawabanMurid()) {
            // SOFT ARCHIVE
            $praktikum->update(['is_aktif' => false]);
            $praktikum->soalPraktikums()->update(['is_aktif' => false]);
            
            session()->flash('warning', 'Praktikum tidak dapat dihapus permanen karena murid telah menjawab soal di dalamnya. Data ini telah diarsipkan (Soft-Archive) untuk keperluan penelitian.');
        } else {
            // HARD DELETE dengan manual cascade
            $praktikum->soalPraktikums()->delete();
            $praktikum->delete();
            
            session()->flash('success', 'Modul Praktikum berhasil dihapus secara permanen.');
        }

        $this->loadPraktikum();
    }

    public function render()
    {
        return view('livewire.guru.praktikum.index')
            ->layout('layouts.app', ['title' => 'Manajemen Praktikum PBL']);
    }
}
