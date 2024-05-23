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
        Schema::table('transaksi_asset_masuk', function (Blueprint $table) {
            $table->foreign('id_asset')->references(['id_asset'])->on('asset')->onDelete('cascade');
            $table->foreign('id_user')->references(['id'])->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_asset_masuk', function (Blueprint $table) {
            $table->dropForeign(['id_asset']);
            $table->dropForeign(['id_user']);
        });
    }
};
