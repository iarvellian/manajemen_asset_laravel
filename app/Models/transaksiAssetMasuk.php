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
        'id_user',
        'tgl_masuk',
        'keterangan'
    ];

    public function asset()
    {
        return $this->belongsTo(asset::class, 'id_asset');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
