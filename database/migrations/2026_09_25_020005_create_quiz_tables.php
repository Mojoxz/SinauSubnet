<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materi_id')->constrained('materi')->restrictOnDelete();
            $table->string('judul');
            $table->unsignedSmallInteger('durasi_menit')->default(30);
            $table->unsignedSmallInteger('bonus_kecepatan_maks')->default(50)
                  ->comment('Poin bonus maksimum yang bisa didapat dari faktor kecepatan');
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->index(['materi_id', 'is_aktif']);
        });

        Schema::create('soal_quiz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quiz')->restrictOnDelete();
            $table->text('pertanyaan');
            $table->string('tipe', 20)->comment('pilihan_ganda | isian | uraian');
            $table->json('opsi')->nullable()->comment('Array pilihan [{huruf, teks}] — hanya untuk pilihan_ganda');
            $table->unsignedSmallInteger('poin_dasar')->default(10)
                  ->comment('Poin gamifikasi dasar jika jawaban benar (PG/isian saja)');
            $table->string('level_bloom', 5)->comment('C3 | C4 | C5');
            $table->unsignedSmallInteger('skor_maks');
            $table->json('kunci_jawaban')->comment('Array jawaban benar (PG: ["A"], isian: ["27","/27"])');
            $table->json('rubrik')->nullable()->comment('Array kriteria [{kriteria, skor_maks}] — hanya uraian');
            $table->text('pembahasan')->nullable();
            $table->text('hint')->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->index(['quiz_id', 'is_aktif']);
        });

        Schema::create('hasil_quiz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quiz')->restrictOnDelete();
            $table->foreignId('murid_id')->constrained('murid')->cascadeOnDelete();
            $table->timestamp('waktu_mulai');
            $table->timestamp('waktu_selesai')->nullable();
            $table->unsignedSmallInteger('skor_total')->default(0);
            $table->unsignedSmallInteger('poin_total')->default(0)->comment('Termasuk bonus kecepatan');
            $table->timestamps();

            $table->unique(['quiz_id', 'murid_id']);
            $table->index('murid_id');
        });

        Schema::create('jawaban_quiz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hasil_quiz_id')->constrained('hasil_quiz')->cascadeOnDelete();
            $table->foreignId('soal_quiz_id')->constrained('soal_quiz')->restrictOnDelete();
            $table->text('jawaban')->nullable();
            $table->unsignedSmallInteger('skor_ai')->nullable();
            $table->unsignedSmallInteger('skor_final')->nullable();
            $table->text('feedback_ai')->nullable();
            $table->text('feedback_final')->nullable();
            $table->string('status_penilaian', 20)->default('final')
                  ->comment('final | menunggu_ai | dinilai_ai | divalidasi_guru | dikoreksi_guru | perlu_manual');
            $table->json('detail_skor_ai')->nullable();
            $table->unsignedTinyInteger('jumlah_percobaan_ai')->default(0);
            $table->boolean('sampel_review')->default(false)->comment('Permanen setelah dinilai_ai — tidak berubah');
            $table->foreignId('ditinjau_oleh')->nullable()->constrained('guru')->nullOnDelete();
            $table->timestamp('ditinjau_pada')->nullable();
            $table->timestamps();

            $table->unique(['hasil_quiz_id', 'soal_quiz_id']);
            $table->index(['status_penilaian', 'sampel_review']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_quiz');
        Schema::dropIfExists('hasil_quiz');
        Schema::dropIfExists('soal_quiz');
        Schema::dropIfExists('quiz');
    }
};
