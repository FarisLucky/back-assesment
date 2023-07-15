<?php

namespace App\Http\Controllers;

use App\Models\MSubPenilaian;
use App\Http\Requests\StoreMSubPenilaianRequest;
use App\Http\Requests\UpdateMSubPenilaianRequest;
use App\Http\Resources\Api\MSubPenilaianResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class MSubPenilaianController extends Controller
{
    public function index()
    {
        $page = request('per_page');
        $columnKeyFilter = request('column_key');
        $columnValFilter = request('column_val');
        $sortBy = request('sort_by');
        $sortType = request('sort_type');

        $subPenilaians = MSubPenilaian::leftJoin('m_penilaian', 'm_penilaian.id', '=', 'm_sub_penilaian.id_penilaian')
            ->leftJoin('m_jabatan as jabatan_penilai', 'jabatan_penilai.id', '=', 'm_sub_penilaian.id_jabatan_penilai')
            ->leftJoin('m_jabatan as jabatan_kinerja', 'jabatan_kinerja.id', '=', 'm_sub_penilaian.id_jabatan_kinerja')
            ->select(
                'm_sub_penilaian.id',
                'm_sub_penilaian.nama',
                'm_sub_penilaian.created_at',
                'm_penilaian.tipe as penilaian_tipe',
                'm_penilaian.nama as penilaian_nama',
                'jabatan_penilai.nama as jabatan_penilai_nama',
                'jabatan_kinerja.nama as jabatan_kinerja_nama',
            );

        $subPenilaians->when(!is_null($columnKeyFilter) && !is_null($columnValFilter), function ($query) use ($columnKeyFilter, $columnValFilter) {

            for ($i = 0; $i < count($columnKeyFilter); $i++) {

                $table = 'm_sub_penilaian';

                $column = $columnKeyFilter[$i];

                $explodeColumn = explode('_', $columnKeyFilter[$i]);

                if (str_contains($column, '_')) {
                    $column = 'm_' . str_replace('_', '.', $columnKeyFilter[$i]); // nama table.column = jabatan_nama -> m_jabatan.nama
                } else if (count($explodeColumn) > 2) {
                    $columnName = end($explodeColumn);
                    $tableName = $explodeColumn[0] . '_' . $explodeColumn[1];
                    $column =  $tableName . '.' . $columnName;
                } else {
                    $column =  $table . '.' . $column;
                }

                $val = $columnValFilter[$i];

                $query->where($column, 'LIKE', "%{$val}%");
            }
        });

        $subPenilaians->when(!is_null($sortBy) && !is_null($sortType), function ($query) use ($sortBy, $sortType) {
            $query->orderBy($sortBy, $sortType);
        }, function ($query) {
            $query->orderBy('id', 'desc');
        });

        return MSubPenilaianResource::collection(
            $subPenilaians->latest()->paginate($page)
        );
    }

    public function data()
    {
        $units = MSubPenilaian::with('penilaian')
            ->select(
                'id',
                'nama',
                'id_jabatan_penilai',
                'id_jabatan_kinerja',
                'id_unit_penilai',
            )
            ->get();

        return MSubPenilaianResource::collection($units);
    }
    public function dataKhusus()
    {
        $units = MSubPenilaian::with([
            'penilaian'
        ])
            ->select(
                'id',
                'nama',
                'id_jabatan_penilai',
                'id_jabatan_kinerja',
                'id_unit_penilai',
            )
            ->get();

        return MSubPenilaianResource::collection($units);
    }

    public function store(StoreMSubPenilaianRequest $request)
    {
        $data = $request->validated();

        $formData = [];

        foreach ($data['nama'] as $nama) {
            if (!is_null($nama['nama']) || $nama['nama'] != '') {
                array_push($formData, [
                    'nama' => strtoupper($nama['nama']),
                    'id_penilaian' => $data['id_penilaian'],
                    'id_jabatan_penilai' => !is_null(optional($data)['id_jabatan_penilai']) ? $data['id_jabatan_penilai'] : null,
                    'id_jabatan_kinerja' => !is_null(optional($data)['id_jabatan_kinerja']) ? $data['id_jabatan_kinerja'] : null,
                    'id_unit_penilai' => !is_null(optional($data)['id_unit_penilai']) ? $data['id_unit_penilai'] : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'kategori' => $data['kategori'],
                ]);
            }
        }

        try {
            DB::beginTransaction();

            MSubPenilaian::insert($formData);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }

        return response()->json(
            'Berhasil ditambahkan !',
            Response::HTTP_OK
        );
    }

    public function show($id)
    {
        $mSubPenilaian = MSubPenilaian::findOrFail($id);

        return response()->json(
            new MSubPenilaianResource($mSubPenilaian),
            Response::HTTP_OK
        );
    }

    public function update(UpdateMSubPenilaianRequest $request, $id)
    {
        $mSubPenilaian = MSubPenilaian::findOrFail($id);

        $data = $request->validated();

        $data = array_merge($data, ['nama' => $request->nama[0]['nama']]);

        if ($request->has('id_jabatan_penilai')) {
            $data = array_merge($data, ['id_jabatan_penilai' => $request->id_jabatan_penilai]);
        }

        if ($request->has('id_jabatan_kinerja')) {
            $data = array_merge($data, ['id_jabatan_kinerja' => $request->id_jabatan_kinerja]);
        }

        $mSubPenilaian->update($data);

        return response()->json(
            'Berhasil diubah !',
            Response::HTTP_OK
        );
    }

    public function destroy($id)
    {

        $mSubPenilaian = MSubPenilaian::findOrFail($id);
        $mSubPenilaian->delete();

        return response()->json('', Response::HTTP_NO_CONTENT);
    }
}
