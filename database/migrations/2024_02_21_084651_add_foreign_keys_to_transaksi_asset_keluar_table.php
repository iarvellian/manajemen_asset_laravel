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
        Schema::table('transaksi_asset_keluar', function (Blueprint $table) {
            $table->foreign('id_asset')->references(['id_asset'])->on('asset');
            $table->foreign('id')->references(['id'])->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_asset_keluar', function (Blueprint $table) {
            $table->dropForeign(['id_asset']);
            $table->dropForeign(['id']);
        });
    }
};
