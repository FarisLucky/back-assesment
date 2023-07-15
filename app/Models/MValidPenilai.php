<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MValidPenilai extends Model
{
    protected $table = "m_valid_penilai";

    protected $fillable = [
        'id_sub',
        'id_jabatan_penilai',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
