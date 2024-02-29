<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ChangeLoggingTrait;

class transaksiAssetMasuk extends Model
{
    use HasFactory, ChangeLoggingTrait;

    protected $primaryKey = 'id_transaksi_masuk';
    protected $table = 'transaksi_asset_masuk';
    protected $fillable = [
        'id_transaksi_masuk',
        'id_asset',
        'id',
        'tgl_masuk',
        'keterangan'
    ];
}
