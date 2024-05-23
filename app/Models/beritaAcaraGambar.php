<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class beritaAcaraGambar extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_gambar';
    protected $table = 'berita_acara_gambar';
    protected $fillable = [
        'id_berita_acara',
        'path',
    ];

    public function beritaAcara()
    {
        return $this->belongsTo(BeritaAcara::class, 'id_berita_acara');
    }
}
