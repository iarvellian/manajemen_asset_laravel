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
        'pihak_pertama',
        'pihak_kedua',
        'jabatan_pertama',
        'jabatan_kedua',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function assets()
    {
        return $this->belongsToMany(asset::class, 'asset_berita_acara_link', 'id_berita_acara', 'id_asset');
    }

    public function images()
    {
        return $this->hasMany(beritaAcaraGambar::class, 'id_berita_acara');
    }
}
