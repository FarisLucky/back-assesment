<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Arr;

trait SearchingTrait
{
    public function getRelations(string $class)
    {
        return Arr::get(config('relationship'), $class);
    }

    public function checkRelation(string $column)
    {
        return str_contains($column, '.');
    }

    public function explodeColumnName(string $column)
    {
        return explode('.', $column);
    }

    public function searchRelation(array $relations, string $key)
    {
        $result =  Arr::where($relations, function ($value) use ($key) {
            return $value == $key;
        });

        return $result;
    }

    public function keyRelationFirst($column)
    {
        return array_keys($column)[0];
    }
}
