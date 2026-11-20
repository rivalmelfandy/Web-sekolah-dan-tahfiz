<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('target_hafalan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id');
            $table->foreignId('ustadz_id')->nullable();
            $table->string('surah_awal', 100);
            $table->string('surah_akhir', 100);
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();

            // Indeks komposit (siswa_id, status) melayani FK siswa_id.
            $table->index(['siswa_id', 'status']);
            $table->foreign('siswa_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('ustadz_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_hafalan');
    }
};
