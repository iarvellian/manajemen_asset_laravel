<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class refDivisi extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_divisi';
    protected $keyType = 'string';
    protected $table = 'ref_divisi';
    protected $fillable = [
        'id_divisi',
        'nama_divisi'
    ];

}
