<?php

class FilterTrait
{
    public function columnKey($key, $iteration)
    {
        $table = 'm_karyawan';

        $column = $key[$i];

        if (str_contains($column, '_')) {
            $column = 'm_' . str_replace('_', '.', $key[$i]); // nama table.column = jabatan_nama -> m_jabatan.nama
        } else {
            $column =  $table . '.' . $column;
        }

        return [
            'columnKey' => $key,
            'columnVal' => $val[$i]
        ];
    }
}
