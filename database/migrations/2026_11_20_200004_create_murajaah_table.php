<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Mengikuti skema DBML terbaru:
     *   - nilai_murajaah (tinyInteger, default 80, not null)
     *     menggantikan kolom enum `status_mutqin` yang lama.
     *   - Indeks huruf dihitung di aplikasi melalui NilaiIndeks::dariNilai().
     */
    public function up(): void
    {
        Schema::create('murajaah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setoran_id');
            $table->foreignId('ustadz_id')->nullable();
            $table->string('ayat_range', 50)->nullable();
            $table->tinyInteger('nilai_murajaah')->unsigned()->default(80);
            $table->text('catatan')->nullable();
            $table->date('tanggal_murajaah');
            $table->timestamps();

            // Indeks komposit (setoran_id, tanggal_murajaah) melayani FK setoran_id.
            $table->index(['setoran_id', 'tanggal_murajaah']);
            $table->foreign('setoran_id')->references('id')->on('setoran_hafalan')->cascadeOnDelete();
            $table->foreign('ustadz_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('murajaah');
    }
};
