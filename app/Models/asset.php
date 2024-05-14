<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ChangeLoggingTrait;

class asset extends Model
{
    use HasFactory, ChangeLoggingTrait;

    protected $primaryKey = 'id_asset';
    protected $table = 'asset';
    protected $fillable = [
        'id_asset',
        'id_divisi',
        'id_lokasi',
        'id_kelas_aset', 
        'id_kode_projek',
        'is_luar_kota',
        'thn_perolehan',
        'cost_center',
        'ue',
        'kode_aset', 
        'nama_aset',
        'jumlah_sap',
        'jumlah_fisik',
        'kondisi',
        'pic_aset',
        'pic_project',
        'serial_number',
        'no_rangka_kendaraan', 
        'no_mesin_kendaraan',
        'no_plat_kendaraan',
        'is_luar_kota'
    ];

    public function divisi()
    {
        return $this->belongsTo(refDivisi::class, 'id_divisi');
    }

    public function kelasAset()
    {
        return $this->belongsTo(refKelasAset::class, 'id_kelas_aset');
    }

    public function kodeProjek()
    {
        return $this->belongsTo(refKodeProjek::class, 'id_kode_projek');
    }

    public function lokasi()
    {
        return $this->belongsTo(refLokasi::class, 'id_lokasi');
    }

    public function beritaAcara()
    {
        return $this->belongsToMany(beritaAcara::class, 'asset_berita_acara', 'asset_id', 'berita_acara_id');
    }
}
