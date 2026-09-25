<?php

declare(strict_types=1);

namespace App\Livewire\Guru\Materi;

use App\Models\Materi;
use Livewire\Component;

class Buat extends Component
{
    public $level = 1;
    public $urutan = 1;
    public $judul = '';
    public $konten = '';
    public $is_aktif = true;

    protected $rules = [
        'level' => 'required|integer|min:1|max:4',
        'urutan' => 'required|integer|min:1',
        'judul' => 'required|string|max:255',
        'konten' => 'required|string',
    ];

    public function mount()
    {
        $this->authorize('create', Materi::class);
        
        // Ambil urutan terakhir untuk level 1 sebagai default
        $this->urutan = Materi::where('guru_id', auth()->user()->guru->id)
                              ->where('level', 1)
                              ->max('urutan') + 1;
    }

    public function updatedLevel($value)
    {
        // Sesuaikan urutan default setiap ganti level
        $this->urutan = Materi::where('guru_id', auth()->user()->guru->id)
                              ->where('level', $value)
                              ->max('urutan') + 1;
    }

    public function simpan()
    {
        $this->validate();

        Materi::create([
            'guru_id' => auth()->user()->guru->id,
            'level' => $this->level,
            'urutan' => $this->urutan,
            'judul' => $this->judul,
            'konten' => $this->konten,
            'is_aktif' => $this->is_aktif,
        ]);

        session()->flash('success', 'Materi berhasil ditambahkan.');
        return $this->redirectRoute('guru.materi.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.guru.materi.buat')
            ->layout('layouts.app', ['title' => 'Buat Materi Baru']);
    }
}
