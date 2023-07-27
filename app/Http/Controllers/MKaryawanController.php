<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMKaryawanRequest;
use App\Http\Requests\UpdateMKaryawanRequest;
use App\Http\Resources\Api\MKaryawanResource;
use App\Models\MKaryawan;
use Illuminate\Http\Response;

class MKaryawanController extends Controller
{
    public function index()
    {
        $page = request('per_page');
        $columnKeyFilter = request('column_key');
        $columnValFilter = request('column_val');
        $sortBy = request('sort_by');
        $sortType = request('sort_type');

        // $karyawans = MKaryawan::search($columnValFilter);

        $karyawans = MKaryawan::leftJoin('m_jabatan', 'm_jabatan.id', '=', 'm_karyawan.id_jabatan')
            ->selectRaw('
                m_karyawan.id,
                m_karyawan.nama,
                m_karyawan.nip,
                m_karyawan.alamat,
                m_karyawan.pendidikan,
                m_karyawan.created_at,
                m_jabatan.nama as jabatan_nama
            ');

        $karyawans->when(!is_null($columnKeyFilter) && !is_null($columnValFilter), function ($query) use ($columnKeyFilter, $columnValFilter) {

            for ($i = 0; $i < count($columnKeyFilter); $i++) {

                $table = 'm_karyawan';

                $column = $columnKeyFilter[$i];

                if (str_contains($column, '_')) {
                    $column = 'm_' . str_replace('_', '.', $columnKeyFilter[$i]); // nama table.column = jabatan_nama -> m_jabatan.nama
                } else {
                    $column =  $table . '.' . $column;
                }

                $val = $columnValFilter[$i];

                $query->where($column, 'LIKE', "%{$val}%");
            }
        });

        $karyawans->when(!is_null($sortBy) && !is_null($sortType), function ($query) use ($sortBy, $sortType) {
            $query->orderBy($sortBy, $sortType);
        }, function ($query) {
            $query->orderBy('id', 'desc');
        });

        return MKaryawanResource::collection($karyawans->latest()->paginate($page));
    }

    public function data()
    {
        $penilaianKaryawans = MKaryawan::latest()->get();

        return MKaryawanResource::collection($penilaianKaryawans);
    }

    public function store(StoreMKaryawanRequest $request)
    {
        $data = $request->validated();

        $karyawan = MKaryawan::create($data);

        return response()->json(
            new MKaryawanResource($karyawan),
            Response::HTTP_OK
        );
    }

    public function show($id)
    {
        $karyawan = MKaryawan::with('jabatan')->findOrFail($id);

        return response()->json(
            new MKaryawanResource($karyawan),
            Response::HTTP_OK
        );
    }

    public function update(UpdateMKaryawanRequest $request, $id)
    {
        $karyawan = MKaryawan::findOrFail($id);
        $karyawan->update($request->validated());

        return response()->json(
            new MKaryawanResource($karyawan),
            Response::HTTP_OK
        );
    }

    public function destroy($id)
    {
        $karyawan = MKaryawan::findOrFail($id);
        $karyawan->delete();

        return response()->json('', Response::HTTP_NO_CONTENT);
    }
}
