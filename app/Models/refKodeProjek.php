<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class refKodeProjek extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_kode_projek';
    protected $table = 'ref_kode_projek';
    protected $fillable = [
        'id_kode_projek',
        'nama_kode_projek',
    ];
}
