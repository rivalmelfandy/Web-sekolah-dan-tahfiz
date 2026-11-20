<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel `users` mengikuti skema DBML: login memakai nip_nis + password,
     * tanpa kolom email/username/status. Foreign key halaqah_id ditambahkan
     * terpisah (migration add_halaqah_id_foreign) karena adanya referensi
     * melingkar antara users <-> halaqah.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nip_nis', 30)->unique();
            $table->string('password');
            $table->string('nama_lengkap', 100);
            $table->string('no_hp', 20)->nullable();
            $table->enum('role', ['admin', 'ustadz', 'siswa'])->index();
            $table->foreignId('halaqah_id')->nullable()->index();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
