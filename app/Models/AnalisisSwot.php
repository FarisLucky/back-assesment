<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnalisisSwot extends Model
{
    use SoftDeletes;

    protected $table = "analisis_swot";

    protected $fillable = [
        'id_pk',
        'kelebihan',
        'kekurangan',
        'kesempatan',
        'ancaman',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function penilaian()
    {
        return $this->belongsTo(PenilaianKaryawan::class, 'id_pk');
    }
}
