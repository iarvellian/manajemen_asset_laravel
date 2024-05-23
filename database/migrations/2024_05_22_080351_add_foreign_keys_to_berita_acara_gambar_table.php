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
        Schema::table('berita_acara_gambar', function (Blueprint $table) {
            $table->foreign('id_berita_acara')->references(['id_berita_acara'])->on('berita_acara')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berita_acara_gambar', function (Blueprint $table) {
            $table->dropForeign(['id_berita_acara']);
        });
    }
};
