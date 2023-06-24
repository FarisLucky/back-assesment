<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubPenilaianKaryawan extends Model
{
    use SoftDeletes;

    protected $table = "sub_detail_penilaian";

    protected $fillable = [
        'id_detail',
        'penilaian',
        'sub_penilaian',
        'nilai',
        "updated_by",
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
