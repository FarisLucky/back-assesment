<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MUnit extends Model
{
    use SoftDeletes;

    protected $table = "m_unit";

    protected $fillable = [
        'id',
        'nama',
        'id_parent',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
