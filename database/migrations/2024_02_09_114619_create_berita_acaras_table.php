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
        Schema::create('berita_acara', function (Blueprint $table) {
            $table->bigIncrements('id_berita_acara');
            $table->enum('jenis', ['masuk', 'keluar', 'rusak']);
            $table->string('nomor_berita_acara');
            $table->string('perihal');
            $table->string('lokasi');
            $table->dateTime('tgl_cetak');
            $table->string('pihak1');
            $table->string('pihak2');
            $table->string('jabatan1');
            $table->string('jabatan2');
            $table->text('keterangan')->nullable();
            $table->json('assets')->nullable();
            $table->json('gambar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita_acara');
    }
};
