<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nip', 30)->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('murid', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('kelas', 30);
            $table->unsignedInteger('total_poin')->default(0)->comment('Cache — sumber kebenaran ada di riwayat_poin');
            $table->timestamp('poin_dicapai_pada')->nullable()->comment('Tie-breaker leaderboard: kapan poin ini pertama dicapai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('murid');
        Schema::dropIfExists('guru');
    }
};
