<?php

namespace App\Services;

use App\Models\MKaryawan;
use App\Models\MPenilaian;
use App\Models\MSubPenilaian;
use App\Models\PenilaianKaryawan;

class PenilaianKaryawanServices
{
    public function getSubDetailMaster($params)
    {
        $where = array_keys($params['sub_penilaian']);

        $subDetail = MSubPenilaian::with(
            [
                'penilaian' => function ($query) {
                    $query->select('nama', 'tipe');
                },
            ]
        )
            ->whereIn('id_penilaian', $where)
            ->get();

        return $subDetail;
    }

    public function getKaryawan($id)
    {
        $karyawan = MKaryawan::with(
            [
                'jabatan' => function ($query) {
                    $query->select('id', 'nama');
                }
            ]
        )->findOrFail($id, ['id', 'nama', 'id_jabatan']);

        return $karyawan;
    }

    public function subDetailData($params)
    {
        return [
            'id_detail' => $params['id_detail'],
            'penilaian' => $params['penilaian'],
            'sub_penilaian' => $params['sub_penilaian'],
            'nilai' => $params['nilai'],
            'updated_by' => $params['updated_by']
        ];
    }
}
