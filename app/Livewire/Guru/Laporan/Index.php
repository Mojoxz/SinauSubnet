<?php

declare(strict_types=1);

namespace App\Livewire\Guru\Laporan;

use App\Models\Murid;
use App\Models\Materi;
use App\Models\Quiz;
use App\Models\Praktikum;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Filter Form
    public $tipeLaporan = 'nilai_akhir'; // nilai_akhir, hasil_quiz, hasil_praktikum
    public $kelas = '';
    public $modulId = ''; // id quiz atau praktikum

    public function render()
    {
        // 1. Ambil opsi filter kelas
        $daftarKelas = Murid::select('kelas')->distinct()->pluck('kelas');

        // 2. Ambil opsi filter modul berdasarkan tipe
        $daftarModul = collect();
        if ($this->tipeLaporan === 'hasil_quiz') {
            $daftarModul = Quiz::with('materi')->get();
        } elseif ($this->tipeLaporan === 'hasil_praktikum') {
            $daftarModul = Praktikum::with('materi')->get();
        }

        // 3. Query Builder List (Data Preview)
        // Kita tampilkan list murid sebagai preview, ekspor akan menghasilkan file asli
        $query = Murid::with('user');
        
        if ($this->kelas !== '') {
            $query->where('kelas', $this->kelas);
        }

        $murids = $query->orderBy('kelas')->paginate(15);

        return view('livewire.guru.laporan.index', [
            'daftarKelas' => $daftarKelas,
            'daftarModul' => $daftarModul,
            'murids' => $murids
        ])->layout('layouts.app', ['title' => 'Monitoring Laporan & Ekspor Nilai']);
    }

    public function eksporExcel()
    {
        return redirect()->route('guru.ekspor.excel', [
            'tipe' => $this->tipeLaporan,
            'kelas' => $this->kelas,
            'modul' => $this->modulId
        ]);
    }

    public function eksporPdfIndividu($muridId)
    {
        return redirect()->route('guru.ekspor.pdf', ['murid_id' => $muridId]);
    }
}
