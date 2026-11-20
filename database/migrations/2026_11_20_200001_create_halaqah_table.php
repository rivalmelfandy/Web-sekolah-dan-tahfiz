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
        Schema::create('halaqah', function (Blueprint $table) {
            $table->id();
            $table->string('kode_halaqah', 20)->unique();
            $table->string('nama_halaqah', 50)->unique();
            $table->foreignId('ustadz_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('target_juz', 50)->default('Juz 29 - 30');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('halaqah');
    }
};
