<?php

declare(strict_types=1);

namespace App\Livewire\Guru\Materi;

use App\Models\Materi;
use Livewire\Component;

class Ubah extends Component
{
    public Materi $materi;

    public $level;
    public $urutan;
    public $judul;
    public $konten;
    public $is_aktif;

    protected $rules = [
        'level' => 'required|integer|min:1|max:4',
        'urutan' => 'required|integer|min:1',
        'judul' => 'required|string|max:255',
        'konten' => 'required|string',
        'is_aktif' => 'boolean',
    ];

    public function mount(Materi $materi)
    {
        $this->authorize('update', $materi);

        $this->materi = $materi;
        $this->level = $materi->level;
        $this->urutan = $materi->urutan;
        $this->judul = $materi->judul;
        $this->konten = $materi->konten;
        $this->is_aktif = $materi->is_aktif;
    }

    public function simpan()
    {
        $this->validate();

        $this->materi->update([
            'level' => $this->level,
            'urutan' => $this->urutan,
            'judul' => $this->judul,
            'konten' => $this->konten,
            'is_aktif' => $this->is_aktif,
        ]);

        session()->flash('success', 'Materi berhasil diperbarui.');
        return $this->redirectRoute('guru.materi.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.guru.materi.ubah')
            ->layout('layouts.app', ['title' => 'Ubah Materi']);
    }
}
