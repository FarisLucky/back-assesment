<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MJabatan extends Model
{
    use SoftDeletes;

    protected $table = "m_jabatan";

    protected $fillable = [
        'nama',
        'id_parent',
        'level',
    ];

    public $casts = [
        "created_at" => "datetime",
        "updated_at" => "datetime",
    ];

    protected $appends = ['level_custom'];

    public function jabatan()
    {
        return $this->belongsTo(MJabatan::class, 'id_parent');
    }

    public function parentJabatan()
    {
        return $this->belongsTo(MJabatan::class, 'id_parent')->with('jabatan');
    }

    public function getLevelCustomAttribute()
    {
        return $this->getParent($this->id);
    }

    public function getParent($id, $depth = 0)
    {
        $model = MJabatan::where('id', $id)->first();
        if (!is_null($model)) {
            $depth++;

            return $this->getParent($model->id_parent, $depth);
        } else {
            return $depth;
        }
    }
}
