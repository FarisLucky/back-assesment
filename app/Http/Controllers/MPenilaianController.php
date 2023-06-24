<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMPenilaianRequest;
use App\Http\Requests\UpdateMPenilaianRequest;
use App\Http\Resources\Api\MPenilaianResource;
use App\Models\MJabatan;
use App\Models\MPenilaian;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class MPenilaianController extends Controller
{

    public function index()
    {
        $page = request('per_page');
        $columnKeyFilter = request('column_key');
        $columnValFilter = request('column_val');
        $sortBy = request('sort_by');
        $sortType = request('sort_type');

        $penilaians = MPenilaian::with('jabatan');

        $penilaians->when(!is_null($columnKeyFilter) && !is_null($columnValFilter), function ($query) use ($columnKeyFilter, $columnValFilter) {
            for ($i = 0; $i < count($columnKeyFilter); $i++) {
                $query->where($columnKeyFilter[$i], 'LIKE', "%{$columnValFilter[$i]}%");
            }
        });

        $penilaians->when(!is_null($sortBy) && !is_null($sortType), function ($query) use ($sortBy, $sortType) {
            $query->orderBy($sortBy, $sortType);
        }, function ($query) {
            $query->orderBy('id', 'desc');
        });

        return MPenilaianResource::collection($penilaians->latest()->paginate($page));
    }

    public function data()
    {
        $penilaian = MPenilaian::all([
            'id', 'nama', 'tipe', 'level'
        ]);

        return response()->json(
            MPenilaianResource::collection($penilaian),
            Response::HTTP_OK
        );
    }

    public function store(StoreMPenilaianRequest $request)
    {
        $data = $request->validated();

        if ($data['tipe'] == MPenilaian::TIPE[0]) { // tipe umum
            $data['level'] = $request->level;
        }

        try {
            DB::beginTransaction();

            MPenilaian::create($data);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }

        return response()->json(
            'Tindakan Berhasil',
            Response::HTTP_OK
        );
    }

    public function show(MPenilaian $penilaian)
    {
        return response()->json(
            new MPenilaianResource($penilaian),
            Response::HTTP_OK
        );
    }

    public function update(UpdateMPenilaianRequest $request, MPenilaian $penilaian)
    {
        $data = $request->validated();
        $idJabatanPenilai = $request->id_jabatan_penilai;

        if (!is_null($idJabatanPenilai)) {

            $getLevel = MJabatan::find($idJabatanPenilai, ['id', 'level']);

            $data = array_merge($data, [
                'id_jabatan_penilai' => request('id_jabatan_penilai'),
                'level' => $getLevel->level
            ]);

            if ($data['tipe'] == MPenilaian::TIPE[0]) { // tipe umum
                $data['id_jabatan_penilai'] = null;
            }
        }

        $penilaian->update($data);

        return response()->json(
            'Tindakan Berhasil',
            Response::HTTP_OK
        );
    }

    public function destroy(MPenilaian $penilaian)
    {
        //return response()->json($penilaian);
        $penilaian->delete();

        return response()->json('', Response::HTTP_NO_CONTENT);
    }
}
