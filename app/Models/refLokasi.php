<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class refLokasi extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_lokasi';
    protected $keyType = 'string';
    protected $table = 'ref_lokasi';
    protected $fillable = [
        'id_lokasi',
        'nama_lokasi',
    ];
}
