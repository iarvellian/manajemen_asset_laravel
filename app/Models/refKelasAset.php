<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ChangeLoggingTrait;

class refKelasAset extends Model
{
    use HasFactory, ChangeLoggingTrait;

    protected $primaryKey = 'id_kelas_aset';
    protected $table = 'ref_kelas_aset';
    protected $fillable = [
        'id_kelas_aset',
        'nama_kelas_aset',
    ];
}
