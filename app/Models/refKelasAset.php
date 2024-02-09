<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class refKelasAset extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_kelas_aset';
    protected $keyType = 'string';
    protected $table = 'ref_kelas_aset';
    protected $fillable = [
        'id_kelas_aset',
        'nama_kelas_aset',
    ];
}
