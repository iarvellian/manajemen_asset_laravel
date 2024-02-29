<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ChangeLoggingTrait;

class refRole extends Model
{
    use HasFactory, ChangeLoggingTrait;

    protected $primaryKey = 'id_role';
    protected $table = 'ref_role';
    protected $fillable = [
        'id_role',
        'nama_role',
    ];
}
