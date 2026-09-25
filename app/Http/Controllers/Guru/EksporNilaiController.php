<?php

declare(strict_types=1);

namespace App\Http\Controllers\Guru;

use App\Models\Murid;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

/**
 * Controller ekspor nilai ke Excel.
 * Implementasi penuh dikerjakan di Tahap 6 (Laporan & Ekspor).
 * Saat ini hanya mengembalikan respons placeholder.
 */
class EksporNilaiController
{
    public function __invoke(): Response
    {
        Gate::authorize('laporan.export');

        // TODO: Tahap 6 — implementasi ekspor Excel dengan Maatwebsite/Excel
        return response('Fitur ekspor akan diimplementasikan di Tahap 6.', 200);
    }
}
