<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('praktikum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materi_id')->constrained('materi')->restrictOnDelete();
            $table->string('judul');
            $table->longText('studi_kasus')->comment('Narasi kasus PBL yang diberikan kepada murid');
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->index(['materi_id', 'is_aktif']);
        });

        Schema::create('soal_praktikum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('praktikum_id')->constrained('praktikum')->restrictOnDelete();
            $table->text('pertanyaan');
            $table->string('tipe', 20)->comment('isian | uraian');
            $table->string('level_bloom', 5)->comment('C3 | C4 | C5');
            $table->unsignedSmallInteger('skor_maks');
            $table->json('kunci_jawaban')->comment('Array variasi jawaban benar (isian) atau jawaban ideal (uraian)');
            $table->json('rubrik')->nullable()->comment('Array kriteria [{kriteria, skor_maks}] — hanya untuk uraian');
            $table->text('pembahasan')->nullable();
            $table->json('hint')->nullable()->comment('Array hint bertahap yang dapat dibuka murid satu per satu');
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->index(['praktikum_id', 'is_aktif']);
        });

        Schema::create('jawaban_praktikum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soal_praktikum_id')->constrained('soal_praktikum')->restrictOnDelete();
            $table->foreignId('murid_id')->constrained('murid')->cascadeOnDelete();
            $table->text('jawaban')->nullable();
            $table->string('file_bukti_path')->nullable()->comment('Path relatif di private storage');
            $table->unsignedSmallInteger('skor_ai')->nullable();
            $table->unsignedSmallInteger('skor_final')->nullable();
            $table->text('feedback_ai')->nullable();
            $table->text('feedback_final')->nullable();
            $table->string('status_penilaian', 20)->default('final')
                  ->comment('final | menunggu_ai | dinilai_ai | divalidasi_guru | dikoreksi_guru | perlu_manual');
            $table->json('detail_skor_ai')->nullable()->comment('Skor per kriteria rubrik + alasan dari Gemini');
            $table->unsignedTinyInteger('jumlah_percobaan_ai')->default(0);
            $table->boolean('sampel_review')->default(false)->comment('true jika terpilih sebagai sampel acak 15% — permanen');
            $table->foreignId('ditinjau_oleh')->nullable()->constrained('guru')->nullOnDelete();
            $table->timestamp('ditinjau_pada')->nullable();
            $table->timestamps();

            $table->unique(['soal_praktikum_id', 'murid_id']);
            $table->index(['status_penilaian', 'sampel_review']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_praktikum');
        Schema::dropIfExists('soal_praktikum');
        Schema::dropIfExists('praktikum');
    }
};
