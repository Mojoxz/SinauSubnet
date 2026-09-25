<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ledger poin gamifikasi — satu baris per event sumber poin
        Schema::create('riwayat_poin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('murid_id')->constrained('murid')->cascadeOnDelete();
            $table->morphs('sumber'); // sumber_type + sumber_id
            $table->unsignedSmallInteger('poin');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            // Mencegah duplikasi jika queue job berjalan ulang (idempotent)
            $table->unique(['sumber_type', 'sumber_id']);
        });

        // Progress PBL per murid per materi
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('murid_id')->constrained('murid')->cascadeOnDelete();
            $table->foreignId('materi_id')->constrained('materi')->cascadeOnDelete();
            $table->unsignedTinyInteger('persen_materi')->default(0);
            $table->unsignedTinyInteger('persen_praktikum')->default(0);
            $table->unsignedTinyInteger('persen_quiz')->default(0);
            $table->timestamp('terakhir_diperbarui_pada')->nullable();
            $table->timestamps();

            $table->unique(['murid_id', 'materi_id']);
        });

        // Definisi badge / lencana
        Schema::create('badge', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->text('deskripsi');
            $table->string('ikon')->comment('Emoji atau path file ikon');
            $table->unsignedInteger('syarat_poin')->default(0)
                  ->comment('Total poin murid harus mencapai nilai ini untuk mendapat badge');
            $table->timestamps();
        });

        // Relasi murid ↔ badge yang sudah diperoleh
        Schema::create('murid_badge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('murid_id')->constrained('murid')->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained('badge')->cascadeOnDelete();
            $table->timestamp('diperoleh_pada');
            $table->timestamps();

            $table->unique(['murid_id', 'badge_id']);
        });

        // Feedback guru kepada murid
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->foreignId('murid_id')->constrained('murid')->cascadeOnDelete();
            $table->text('isi');
            $table->timestamp('dibaca_pada')->nullable()->comment('Diisi saat murid pertama kali membuka feedback ini');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
        Schema::dropIfExists('murid_badge');
        Schema::dropIfExists('badge');
        Schema::dropIfExists('progress');
        Schema::dropIfExists('riwayat_poin');
    }
};
