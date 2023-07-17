<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SearchingTrait;
use App\Http\Resources\Api\MValidPenilaiResource;
use App\Models\MValidPenilai;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MValidasiPenilaiController extends Controller
{
    use SearchingTrait;

    public function index()
    {
        $page = request('per_page');
        $columnKeyFilter = request('column_key');
        $columnValFilter = request('column_val');
        $sortBy = request('sort_by');
        $sortType = request('sort_type');

        $validasiPenilaians = MValidPenilai::with([
            'mSubPenilaian' => function ($query) {
                $query->select('id', 'nama');
            },
            'jabatanPenilai' => function ($query) {
                $query->select('id', 'nama');
            }
        ])
            ->select('id', 'id_jabatan_penilai', 'id_sub')
            ->where('id_jabatan_penilai', auth()->user()->karyawan->id_jabatan)
            ->orWhereNull('id_jabatan_penilai');

        $relations = $this->getRelations(MValidPenilai::class);

        $validasiPenilaians->when(!is_null($columnKeyFilter) && !is_null($columnValFilter), function ($query) use ($columnKeyFilter, $columnValFilter, $relations) {
            for ($i = 0; $i < count($columnKeyFilter); $i++) {

                if ($this->checkRelation($columnKeyFilter[$i])) {

                    $relationQuery = $this->explodeColumnName($columnKeyFilter[$i]);

                    $getRelation = $this->searchRelation($relations, $relationQuery[1]);

                    $relation = $this->keyRelationFirst($getRelation);

                    $query->whereHas($relation, function ($query) use ($columnValFilter, $relationQuery, $i) {
                        $query->where($relationQuery[2], 'LIKE', "%{$columnValFilter[$i]}%");
                    });
                } else {
                    $query->where($columnKeyFilter[$i], 'LIKE', "%{$columnValFilter[$i]}%");
                }
            }
        });

        $validasiPenilaians->when(!is_null($sortBy) && !is_null($sortType), function ($query) use ($sortBy, $sortType) {
            $query->orderBy($sortBy, $sortType);
        }, function ($query) {
            $query->orderBy('id', 'desc');
        });

        return MValidPenilaiResource::collection(
            $validasiPenilaians->latest()->paginate($page)
        );
    }

    public function store(Request $request)
    {
        try {
            $data = [
                'id_jabatan_penilai' => $request->user()->karyawan->id_jabatan,
            ];

            foreach ($request->sub_penilaians as $sub) {
                $check = MValidPenilai::where(
                    [
                        'id_sub' => $sub['id'],
                        'id_jabatan_penilai' => $data['id_jabatan_penilai'],
                    ]
                );

                if ($check->count() > 0) {
                    throw new Exception('Sub ' . $sub['nama'] . ' sudah ada');
                    continue;
                }

                MValidPenilai::create([
                    'id_sub' => $sub['id'],
                    'id_jabatan_penilai' => $data['id_jabatan_penilai'],
                ]);
            }

            return response()->json('Tindakan Berhasil !', Response::HTTP_CREATED);
        } catch (\Throwable $th) {

            return response()->json([
                'message' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
