<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaksiAssetKeluar extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_transaksi_keluar';
    protected $table = 'transaksi_asset_keluar';
    protected $fillable = [
        'id_transaksi_keluar',
        'id_asset',
        'id',
        'tgl_keluar',
        'keterangan'
    ];
}
