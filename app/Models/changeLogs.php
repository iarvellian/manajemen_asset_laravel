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

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d M Y');
    }

    public function getUpdatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d M Y');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
