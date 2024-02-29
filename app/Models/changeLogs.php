<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class changeLogs extends Model
{
    use HasFactory;

    protected $table = 'change_log';
    protected $fillable = [
        'nama_tabel',
        'aksi',
        'data_lama',
        'data_baru',
        'user_id',
    ];
}
