<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Ambang Batas Review Koreksi AI
    |--------------------------------------------------------------------------
    | Jawaban uraian dengan skor AI di bawah threshold rendah atau di atas
    | threshold tinggi akan masuk ke antrean review guru secara otomatis.
    | random_sample_percentage: persentase jawaban "dinilai_ai" yang dipilih
    | sebagai sampel acak permanen untuk validasi inter-rater skripsi.
    */

    'ai_review' => [
        'skor_rendah_threshold'    => 0.30,
        'skor_tinggi_threshold'    => 0.95,
        'random_sample_percentage' => 15,
    ],

    /*
    |--------------------------------------------------------------------------
    | Batas Upload File Bukti Praktikum
    |--------------------------------------------------------------------------
    | Satuan: Kilobyte (KB).
    | Tipe yang diterima: JPG/PNG untuk screenshot, .pkt/.pka untuk Cisco PT.
    */

    'upload' => [
        'praktikum_image_max' => 5120,   // 5 MB
        'praktikum_pkt_max'   => 10240,  // 10 MB
        'allowed_image_mimes' => ['image/jpeg', 'image/png'],
        'allowed_pkt_mimes'   => ['application/octet-stream'],
        'allowed_extensions'  => ['jpg', 'jpeg', 'png', 'pkt', 'pka'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Batas Input Jawaban Uraian
    |--------------------------------------------------------------------------
    */

    'jawaban' => [
        'max_essay_chars' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Gemini API
    |--------------------------------------------------------------------------
    | API key dan nama model diambil dari .env agar mudah diganti tanpa
    | mengubah kode. temperature rendah untuk konsistensi penilaian rubrik.
    */

    'gemini' => [
        'api_key'     => env('GEMINI_API_KEY'),
        'model'       => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        'temperature' => 0.2,
        'timeout'     => 30,   // detik
        'max_retries' => 3,
    ],

];
