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
        Schema::create('setoran_hafalan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id');
            $table->foreignId('ustadz_id')->nullable();
            $table->string('surah', 50);
            $table->integer('ayat_awal');
            $table->integer('ayat_akhir');
            $table->enum('kualitas', ['Lancar', 'Kurang Lancar', 'Tidak Lancar']);
            $table->integer('nilai')->default(85);
            $table->text('catatan')->nullable();
            $table->date('tanggal_setor');
            $table->timestamps();

            // Indeks komposit (siswa_id, tanggal_setor) sekaligus melayani
            // foreign key siswa_id (kolom paling kiri), menghindari indeks ganda.
            $table->index(['siswa_id', 'tanggal_setor']);
            $table->foreign('siswa_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('ustadz_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setoran_hafalan');
    }
};
