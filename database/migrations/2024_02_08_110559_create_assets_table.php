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
        Schema::create('asset', function (Blueprint $table) {
            $table->bigIncrements('id_asset');
            $table->bigInteger('id_divisi')->unsigned()->index();
            $table->bigInteger('id_lokasi')->unsigned()->index();
            $table->bigInteger('id_kelas_aset')->unsigned()->index();
            $table->bigInteger('id_kode_projek')->unsigned()->index();
            $table->string('thn_perolehan');
            $table->string('cost_center');
            $table->string('ue');
            $table->string('kode_aset');
            $table->string('nama_aset');
            $table->integer('jumlah_sap');
            $table->integer('jumlah_fisik');
            $table->string('kondisi');
            $table->string('pic_aset');
            $table->string('pic_project');
            $table->string('serial_number');
            $table->string('no_rangka_kendaraan');
            $table->string('no_mesin_kendaraan');
            $table->string('no_plat_kendaraan');
            $table->boolean('is_luar_kota');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset');
    }
};
