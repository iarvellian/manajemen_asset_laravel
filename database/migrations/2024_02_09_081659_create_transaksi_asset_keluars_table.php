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
        Schema::create('transaksi_asset_keluar', function (Blueprint $table) {
            $table->bigIncrements('id_transaksi_keluar');
            $table->bigInteger('id_asset')->unsigned()->index();
            $table->bigInteger('id_user')->unsigned()->index();
            $table->date('tgl_keluar');
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_asset_keluar');
    }
};
