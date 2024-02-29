<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ChangeLoggingTrait;

class refDivisi extends Model
{
    use HasFactory, ChangeLoggingTrait;

    protected $primaryKey = 'id_divisi';
    protected $table = 'ref_divisi';
    protected $fillable = [
        'id_divisi',
        'nama_divisi'
    ];

}
