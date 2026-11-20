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
        Schema::create('murajaah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setoran_id');
            $table->foreignId('ustadz_id')->nullable();
            $table->string('ayat_range', 50)->nullable();
            $table->enum('status_mutqin', ['Lancar', 'Kurang Lancar', 'Tidak Lancar']);
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
