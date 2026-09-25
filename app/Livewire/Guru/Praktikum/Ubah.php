<?php

declare(strict_types=1);

namespace App\Livewire\Guru\Praktikum;

use App\Models\Materi;
use App\Models\Praktikum;
use Livewire\Component;

class Ubah extends Component
{
    public Praktikum $praktikum;

    public $materi_id;
    public $judul;
    public $studi_kasus;
    public $is_aktif;
    
    public $daftarMateri = [];

    protected $rules = [
        'materi_id' => 'required|exists:materi,id',
        'judul' => 'required|string|max:255',
        'studi_kasus' => 'required|string',
        'is_aktif' => 'boolean',
    ];

    public function mount(Praktikum $praktikum)
    {
        $this->authorize('update', $praktikum);

        $this->praktikum = $praktikum;
        $this->materi_id = $praktikum->materi_id;
        $this->judul = $praktikum->judul;
        $this->studi_kasus = $praktikum->studi_kasus;
        $this->is_aktif = $praktikum->is_aktif;

        $this->daftarMateri = Materi::where('guru_id', auth()->user()->guru->id)
            ->orderBy('level')
            ->orderBy('urutan')
            ->get();
    }

    public function simpan()
    {
        $this->validate();

        $this->praktikum->update([
            'materi_id' => $this->materi_id,
            'judul' => $this->judul,
            'studi_kasus' => $this->studi_kasus,
            'is_aktif' => $this->is_aktif,
        ]);

        session()->flash('success', 'Informasi Praktikum berhasil diperbarui.');
        return $this->redirectRoute('guru.praktikum.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.guru.praktikum.ubah')
            ->layout('layouts.app', ['title' => 'Ubah Praktikum']);
    }
}
