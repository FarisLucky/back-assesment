<?php

return [
    'App\Models\MJabatan' => [
        'parent' => 'parent'
    ],
    'App\Models\MKaryawan' => [
        'karyawan' => 'karyawan',
        'jabatan' => 'jabatan',
        'unit' => 'unit',
        'penilaianKaryawanKhusus' => 'pk_khusus',
        'penilaianKaryawan' => 'penilaian_karyawan',
        'penilaianKaryawanUmum' => 'pk_umum',
    ],
    'App\Models\MPenilaian' => [
        'mTipe' => 'tipe_penilaian',
        'subPenilaian' => 'sub_penilaian',
    ],
    'App\Models\MSubPenilaian' => [
        'penilaian' => 'penilaian',
        'jabatanPenilai' => 'jabatan_penilai',
        'jabatanKinerja' => 'jabatan_kinerja',
        'unitPenilai' => 'unit_penilai',
    ],
    'App\Models\MTipePenilaian' => [
        'tipePenilaianByTipe' => 'tipe_penilaian_by_tipe',
        'jabatan' => 'jabatan',
    ],
    'App\Models\MTipe' => [
        'penilaian' => 'm_penilaian',
    ],
    'App\Models\PenilaianKaryawan' => [
        'tipePenilaian' => 'tipe_penilaian',
        'analisisSwot' => 'analisis_swot',
    ],
    'App\Models\DetailPenilaian' => [
        'penilaian' => 'tipe_penilaian',
        'tipePenilaian' => 'analisis_swot',
        'subPenilaian' => 'analisis_swot',
    ],
    'App\Models\SubPenilaianKaryawan' => [
        'penilaian' => 'tipe_penilaian',
        'tipePenilaian' => 'analisis_swot',
        'subPenilaian' => 'analisis_swot',
    ],
];
