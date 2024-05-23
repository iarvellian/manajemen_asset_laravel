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
            $table->string('pihak_pertama');
            $table->string('pihak_kedua');
            $table->string('jabatan_pertama');
            $table->string('jabatan_kedua');
            $table->text('keterangan')->nullable();
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
