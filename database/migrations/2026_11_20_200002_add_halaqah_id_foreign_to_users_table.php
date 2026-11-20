<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan foreign key users.halaqah_id -> halaqah.id secara terpisah
     * karena adanya referensi melingkar dengan halaqah.ustadz_id -> users.id.
     * Indeks pada kolom halaqahah_id sudah dibuat di migration create_users_table.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('halaqah_id')->references('id')->on('halaqah')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['halaqah_id']);
        });
    }
};
