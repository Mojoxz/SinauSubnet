<?php

declare(strict_types=1);

namespace App\Livewire\Guru\Materi;

use App\Models\Materi;
use Livewire\Component;

class Index extends Component
{
    public $materis;

    public function mount()
    {
        $this->loadMateri();
    }

    public function loadMateri()
    {
        // Hanya ambil materi milik guru yang login, urutkan berdasarkan level dan urutan
        $this->materis = Materi::where('guru_id', auth()->user()->guru->id)
            ->orderBy('level')
            ->orderBy('urutan')
            ->get();
    }

    public function deleteMateri($id)
    {
        $materi = Materi::findOrFail($id);
        
        // Autorize action using Policy
        $this->authorize('delete', $materi);

        // Cek Soft-Archive Logika Berjenjang:
        // Apakah materi ini memiliki turunan praktikum atau quiz yang sudah dijawab?
        $materiLengkap = Materi::with(['praktikums', 'quizzes'])->find($id);

        $sudahDijawab = false;

        // Periksa di Praktikum
        foreach ($materiLengkap->praktikums as $praktikum) {
            // Karena jawabanPraktikum terikat ke SoalPraktikum (yang terikat ke Praktikum),
            // relasinya mungkin membutuhkan HasManyThrough.
            // Cara termudah: cek count() via relasi HasManyThrough dari Praktikum -> JawabanPraktikum
            // atau cukup hitung di database langsung
            $jumlahJawabanPraktikum = \App\Models\JawabanPraktikum::whereHas('soalPraktikum', function ($query) use ($praktikum) {
                $query->where('praktikum_id', $praktikum->id);
            })->count();

            if ($jumlahJawabanPraktikum > 0) {
                $sudahDijawab = true;
                break;
            }
        }

        // Periksa di Quiz
        if (!$sudahDijawab) {
            foreach ($materiLengkap->quizzes as $quiz) {
                $jumlahHasilQuiz = \App\Models\HasilQuiz::where('quiz_id', $quiz->id)->count();
                if ($jumlahHasilQuiz > 0) {
                    $sudahDijawab = true;
                    break;
                }
            }
        }

        if ($sudahDijawab) {
            // SOFT ARCHIVE
            $materi->update(['is_aktif' => false]);

            // Soft archive juga anak-anaknya secara kaskade agar konsisten
            foreach ($materi->praktikums as $praktikum) {
                $praktikum->update(['is_aktif' => false]);
                $praktikum->soalPraktikums()->update(['is_aktif' => false]);
            }
            foreach ($materi->quizzes as $quiz) {
                $quiz->update(['is_aktif' => false]);
                $quiz->soalQuizzes()->update(['is_aktif' => false]);
            }

            session()->flash('warning', 'Materi tidak dapat dihapus (Hard Delete) karena telah memiliki rekaman jawaban dari murid. Data ini telah Diarsipkan (Soft-Archive) untuk keperluan penelitian Skripsi.');
        } else {
            // HARD DELETE (Manual Cascade untuk menghindari SQLite FK constraint error)
            foreach ($materi->praktikums as $praktikum) {
                $praktikum->soalPraktikums()->delete();
                $praktikum->delete();
            }
            foreach ($materi->quizzes as $quiz) {
                $quiz->soalQuizzes()->delete();
                $quiz->delete();
            }
            $materi->delete();
            
            session()->flash('success', 'Materi berhasil dihapus secara permanen karena belum ada murid yang mengerjakannya.');
        }

        $this->loadMateri();
    }

    public function render()
    {
        return view('livewire.guru.materi.index')
            ->layout('layouts.app', ['title' => 'Manajemen Materi']);
    }
}
