<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ChangeLoggingTrait;

class refLokasi extends Model
{
    use HasFactory, ChangeLoggingTrait;

    protected $primaryKey = 'id_lokasi';
    protected $table = 'ref_lokasi';
    protected $fillable = [
        'id_lokasi',
        'nama_lokasi',
    ];
}
