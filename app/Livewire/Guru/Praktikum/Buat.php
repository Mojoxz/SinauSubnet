<?php

declare(strict_types=1);

namespace App\Livewire\Guru\Praktikum;

use App\Models\Materi;
use App\Models\Praktikum;
use Livewire\Component;

class Buat extends Component
{
    public $materi_id = '';
    public $judul = '';
    public $studi_kasus = '';
    public $is_aktif = true;
    
    public $daftarMateri = [];

    protected $rules = [
        'materi_id' => 'required|exists:materi,id',
        'judul' => 'required|string|max:255',
        'studi_kasus' => 'required|string',
    ];

    public function mount()
    {
        $this->authorize('create', Praktikum::class);
        
        // Ambil materi guru, utamakan yang masih aktif
        $this->daftarMateri = Materi::where('guru_id', auth()->user()->guru->id)
            ->orderBy('level')
            ->orderBy('urutan')
            ->get();
            
        if ($this->daftarMateri->isNotEmpty()) {
            $this->materi_id = $this->daftarMateri->first()->id;
        }
    }

    public function simpan()
    {
        $this->validate();

        $praktikum = Praktikum::create([
            'materi_id' => $this->materi_id,
            'judul' => $this->judul,
            'studi_kasus' => $this->studi_kasus,
            'is_aktif' => $this->is_aktif,
        ]);

        session()->flash('success', 'Praktikum berhasil ditambahkan. Silakan lengkapi soal-soalnya.');
        return $this->redirectRoute('guru.praktikum.detail', ['praktikum' => $praktikum->id], navigate: true);
    }

    public function render()
    {
        return view('livewire.guru.praktikum.buat')
            ->layout('layouts.app', ['title' => 'Buat Praktikum Baru']);
    }
}
