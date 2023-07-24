<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = "comment";

    protected $fillable = [
        'id_pk',
        'catatan',
        'penilai',
        'dinilai',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
