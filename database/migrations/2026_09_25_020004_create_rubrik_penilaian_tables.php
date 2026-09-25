<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rubrik_praktikum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('praktikum_id')->constrained('praktikum')->cascadeOnDelete();
            $table->string('aspek', 100)->comment('Nama singkat aspek unjuk kerja, mis. "Ketepatan Tabel Subnet"');
            $table->text('indikator')->comment('Deskripsi detail kriteria penilaian 1-4');
            $table->unsignedTinyInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('penilaian_praktikum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('praktikum_id')->constrained('praktikum')->restrictOnDelete();
            $table->foreignId('murid_id')->constrained('murid')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('guru')->restrictOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['praktikum_id', 'murid_id']);
        });

        Schema::create('nilai_aspek', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_praktikum_id')->constrained('penilaian_praktikum')->cascadeOnDelete();
            $table->foreignId('rubrik_praktikum_id')->constrained('rubrik_praktikum')->restrictOnDelete();
            $table->unsignedTinyInteger('skor')->comment('Skala 1-4 sesuai rubrik');
            $table->timestamps();

            $table->unique(['penilaian_praktikum_id', 'rubrik_praktikum_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_aspek');
        Schema::dropIfExists('penilaian_praktikum');
        Schema::dropIfExists('rubrik_praktikum');
    }
};
