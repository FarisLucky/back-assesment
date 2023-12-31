<?php

namespace App\Http\Controllers;

use App\Models\MSubPenilaian;
use App\Http\Requests\StoreMSubPenilaianRequest;
use App\Http\Requests\UpdateMSubPenilaianRequest;
use App\Http\Resources\Api\MSubPenilaianResource;
use App\Models\MJabatan;
use App\Models\MPenilaian;
use App\Models\MValidPenilai;
use Exception;
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
            ->select(
                'm_sub_penilaian.id',
                'm_sub_penilaian.nama',
                'm_sub_penilaian.kategori',
                'm_sub_penilaian.created_at',
                'm_penilaian.tipe as penilaian_tipe',
                'm_penilaian.nama as penilaian_nama',
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
                'id_penilaian',
            )
            ->whereHas('penilaian', function ($query) {
                $query->where('tipe', MPenilaian::TIPE[1]);
            })
            ->whereDoesntHave('mValidasiPenilai', function ($query) {
                $query->where('id_jabatan_penilai', auth()->user()->karyawan->id_jabatan)
                    ->orWhereNull('id_jabatan_penilai');
            })
            ->where('kategori', auth()->user()->karyawan->jabatan->kategori)
            ->orderBy('id')
            ->get();

        return MSubPenilaianResource::collection($units);
    }

    public function store(StoreMSubPenilaianRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $jabatan = MJabatan::find($request->id_jabatan_penilai);

            $sub = MSubPenilaian::where('nama', $request->nama)->first();

            if (is_null($sub)) {
                $subData = [
                    'nama' => strtoupper($data['nama']),
                    'id_penilaian' => $data['id_penilaian'],
                    'kategori' => $jabatan->kategori,
                    'created_by' => $request->user()->id,
                ];

                $sub = MSubPenilaian::create($subData);
            }

            $getValidPenilai = MValidPenilai::where([
                'id_sub' => $sub->id,
                'id_jabatan_penilai' => $jabatan->id,
            ])->first();

            if (!is_null($getValidPenilai)) {

                DB::commit();
                throw new Exception('Penilaian Sudah Ada');
            }

            MValidPenilai::create([
                'id_sub' => $sub->id,
                'id_jabatan_penilai' => $jabatan->id,
            ]);

            DB::commit();

            return response()->json(
                'Berhasil ditambahkan !',
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(
                $th->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
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
