<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->unsignedTinyInteger('level')->comment('1-4, level materi Subnetting');
            $table->string('judul');
            $table->longText('konten')->comment('Konten markdown yang sudah disanitasi saat render');
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->index(['level', 'is_aktif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi');
    }
};
