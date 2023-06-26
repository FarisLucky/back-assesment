<?php

namespace App\Http\Controllers;

use App\Http\Resources\Api\MKaryawanResource;
use App\Http\Resources\Api\PenilaianKaryawanResource;
use App\Models\MKaryawan;
use App\Models\MTipe;
use App\Models\PenilaianKaryawan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HistoryPenilaianController extends Controller
{
    public function index()
    {
        $page = request('per_page');
        $columnKeyFilter = request('column_key');
        $columnValFilter = request('column_val');
        $sortBy = request('sort_by');
        $sortType = request('sort_type');

        $karyawans = MKaryawan::with([
            'penilaianKaryawans' => function ($query) {
                $query->whereMonth('created_at', date('m'))
                    ->whereYear('created_at', date('Y'));
            }
        ]);

        $karyawans->when(!is_null($columnKeyFilter) && !is_null($columnValFilter), function ($query) use ($columnKeyFilter, $columnValFilter) {
            for ($i = 0; $i < count($columnKeyFilter); $i++) {
                $query->where($columnKeyFilter[$i], 'LIKE', "%{$columnValFilter[$i]}%");
            }
        });

        $karyawans->when(!is_null($sortBy) && !is_null($sortType), function ($query) use ($sortBy, $sortType) {
            $query->orderBy($sortBy, $sortType);
        }, function ($query) {
            $query->orderBy('id', 'desc');
        });

        return MKaryawanResource::collection(
            $karyawans->latest()->paginate($page)
        );
    }

    public function store(Request $request)
    {
        //
    }

    public function show($idKaryawan, $tipe, $month, $year)
    {
        try {

            $karyawan = PenilaianKaryawan::with([
                'tipePenilaian', // tipe penilaian relationship
                'tipePenilaian.detail', // tipe penilaian relationship
                'tipePenilaian.detail.subPenilaian', // tipe penilaian relationship
            ])
                ->where(function ($query) use ($tipe, $month, $year) {
                    $query->where('tipe', $tipe)
                        ->whereMonth('tgl_nilai', $month)
                        ->whereYear('tgl_nilai', $year);
                })
                ->where('id_karyawan', $idKaryawan)
                ->firstOrFail();

            return response()->json(
                new PenilaianKaryawanResource($karyawan),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (\Throwable $th) {
            return response()->json(
                $th->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
