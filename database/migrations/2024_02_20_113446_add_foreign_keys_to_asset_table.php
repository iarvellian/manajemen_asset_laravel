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
        Schema::table('asset', function (Blueprint $table) {
            $table->foreign('id_divisi')->references('id_divisi')->on('ref_divisi');
            $table->foreign('id_lokasi')->references('id_lokasi')->on('ref_lokasi');
            $table->foreign('id_kelas_aset')->references('id_kelas_aset')->on('ref_kelas_aset');
            $table->foreign('id_kode_projek')->references('id_kode_projek')->on('ref_kode_projek');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset', function (Blueprint $table) {
            $table->dropForeign(['id_divisi']);
            $table->dropForeign(['id_lokasi']);
            $table->dropForeign(['id_kelas_aset']);
            $table->dropForeign(['id_kode_projek']);
        });
    }
};
