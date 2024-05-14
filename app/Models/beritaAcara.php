<?php

namespace App\Models;

use App\Traits\ChangeLoggingTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class beritaAcara extends Model
{
    use HasFactory, ChangeLoggingTrait;

    protected $primaryKey = 'id_berita_acara';
    protected $table = 'berita_acara';
    protected $fillable = [
        'jenis',
        'nomor_berita_acara',
        'perihal',
        'lokasi',
        'tgl_cetak',
        'pihak1',
        'pihak2',
        'jabatan1',
        'jabatan2',
        'keterangan',
        'gambar',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'gambar' => 'json',
    ];

    public function asset()
    {
        return $this->belongsToMany(asset::class, 'asset_berita_acara', 'berita_acara_id', 'asset_id');
    }
}
