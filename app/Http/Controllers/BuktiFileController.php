<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\JawabanPraktikum;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller untuk streaming/download berkas bukti praktikum
 * dari private storage (screenshot PNG/JPG dan file .pkt/.pka).
 *
 * Otorisasi dilakukan via StoragePolicy sebelum file di-stream.
 */
class BuktiFileController extends Controller
{
    public function __invoke(JawabanPraktikum $jawaban): StreamedResponse
    {
        // Cek izin via StoragePolicy
        Gate::authorize('download-bukti-file', $jawaban);

        abort_if(
            ! $jawaban->file_bukti_path || ! Storage::disk('local')->exists($jawaban->file_bukti_path),
            404,
            'Berkas bukti tidak ditemukan.'
        );

        return Storage::disk('local')->download($jawaban->file_bukti_path);
    }
}
