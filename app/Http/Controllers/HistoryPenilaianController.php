<?php

namespace App\Http\Controllers;

use App\Exports\PenilaianExport;
use App\Http\Resources\Api\MKaryawanResource;
use App\Http\Resources\Api\PenilaianKaryawanResource;
use App\Models\MJabatan;
use App\Models\MKaryawan;
use App\Models\PenilaianKaryawan;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;

class HistoryPenilaianController extends Controller
{
    public function index()
    {
        $page = request('per_page');
        $columnKeyFilter = request('column_key');
        $columnValFilter = request('column_val');
        $sortBy = request('sort_by');
        $sortType = request('sort_type');

        $jabatan = MJabatan::select('id')
            ->where('id_parent', auth()->user()->karyawan->id_jabatan)
            ->get();

        $karyawans = MKaryawan::with([
            'jabatan',
            'penilaianKaryawan'
        ])
            ->where('id', '<>', auth()->user()->id_karyawan);

        $karyawans->when(!is_null($jabatan), function ($query) use ($jabatan) {
            $query->whereIn('id_jabatan', $jabatan->pluck('id'));
        });

        $karyawans->when(!is_null($columnKeyFilter) && !is_null($columnValFilter), function ($query) use ($columnKeyFilter, $columnValFilter) {
            for ($i = 0; $i < count($columnKeyFilter); $i++) {
                if (str_contains($columnKeyFilter[$i], '.')) {

                    $query->whereHas('jabatan', function ($query) use ($columnValFilter, $columnKeyFilter, $i) {
                        $column = explode('.', $columnKeyFilter[$i])[2];
                        $query->where($column, 'LIKE', "%{$columnValFilter[$i]}%");
                    });
                } elseif ($columnKeyFilter[$i] == 'month') {

                    $query->whereHas('penilaianKaryawan', function ($query) use ($columnValFilter, $i) {
                        $query->filterByMonth($columnValFilter[$i])
                            ->filterByYear($columnValFilter[$i + 1]);
                    });
                    break;
                } elseif ($columnKeyFilter[$i] == 'year') {

                    $query->whereHas('penilaianKaryawan', function ($query) use ($columnValFilter, $i) {
                        $query->filterByYear($columnValFilter[$i]);
                    });
                } else {

                    $query->where($columnKeyFilter[$i], 'LIKE', "%{$columnValFilter[$i]}%");
                }
            }
        });

        $karyawans->when(!is_null($sortBy) && !is_null($sortType), function ($query) use ($sortBy, $sortType) {
            $query->orderBy($sortBy, $sortType);
        }, function ($query) {
            $query->orderBy('id', 'desc');
        });

        // return response()->json($karyawans->toSql());

        return MKaryawanResource::collection(
            $karyawans->latest()->paginate($page)
        );
    }

    public function listPenilaians($idKaryawan, $tipe)
    {
        try {
            $penilaians = PenilaianKaryawan::select('id', 'tipe', 'nama_penilai', 'tgl_nilai')
                ->where([
                    'id_karyawan' => $idKaryawan,
                    'tipe' => $tipe
                ])
                ->get();

            return response()->json(PenilaianKaryawanResource::collection($penilaians));
        } catch (\Throwable $th) {

            return response()->json(
                $th->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function showById($id)
    {
        try {

            $penilaian = PenilaianKaryawan::with([
                'tipePenilaian', // tipe penilaian relationship
                'tipePenilaian.detailPenilaian', // tipe penilaian relationship
                'tipePenilaian.detailPenilaian.subPenilaian', // tipe penilaian relationship
                'analisisSwot', // analisis swot relationship
            ])
                ->where('id', $id)
                ->firstOrFail();

            return response()->json(
                new PenilaianKaryawanResource($penilaian),
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            return response()->json(
                $th->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function show($idKaryawan, $tipe, $month, $year)
    {
        try {

            $penilaian = PenilaianKaryawan::with([
                'tipePenilaian', // tipe penilaian relationship
                'tipePenilaian.detailPenilaian', // tipe penilaian relationship
                'tipePenilaian.detailPenilaian.subPenilaian', // tipe penilaian relationship
                'analisisSwot', // analisis swot relationship
            ])
                ->where([
                    'id_karyawan' => $idKaryawan,
                    'tipe' => $tipe,
                ])
                ->orderByDesc('id')
                ->firstOrFail();

            return response()->json(
                new PenilaianKaryawanResource($penilaian),
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            return response()->json(
                $th->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function printUmum($idPenilaian)
    {
        try {
            $nilai = PenilaianKaryawan::with([
                'tipePenilaian', // tipe penilaian relationship
                'tipePenilaian.detailPenilaian', // tipe penilaian relationship
                'tipePenilaian.detailPenilaian.subPenilaian', // tipe penilaian relationship
                'analisisSwot', // analisis swot relationship
            ])
                ->where('id', $idPenilaian)
                ->firstOrFail();

            return view('history.nilai_umum', compact('nilai'));
        } catch (\Throwable $th) {

            return dd($th->getMessage());

            // abort(404, 'MAAF TIDAK ADA DATA');
        }
    }

    public function printKhusus($idPenilaian)
    {
        try {
            $nilai = PenilaianKaryawan::with([
                'karyawan', // tipe penilaian relationship
                'tipePenilaian', // tipe penilaian relationship
                'tipePenilaian.detailPenilaian', // tipe penilaian relationship
                'tipePenilaian.detailPenilaian.subPenilaian', // tipe penilaian relationship
                'analisisSwot', // analisis swot relationship
            ])
                ->where('id', $idPenilaian)
                ->firstOrFail();
            if ($nilai->kategori == MJabatan::NON_MEDIS) {
                $viewName = 'history.nilai_khusus_nonmedis';
            } else {
                $viewName = 'history.nilai_khusus_medis';
            }

            return view($viewName, compact('nilai'));
        } catch (\Throwable $th) {

            return dd($th->getMessage());

            // abort(404, 'MAAF TIDAK ADA DATA');
        }
    }

    public function excelUmum($idPenilaian)
    {
        try {
            $nilai = PenilaianKaryawan::with([
                'tipePenilaian', // tipe penilaian relationship
                'tipePenilaian.detailPenilaian', // tipe penilaian relationship
                'tipePenilaian.detailPenilaian.subPenilaian', // tipe penilaian relationship
                'analisisSwot', // analisis swot relationship
            ])
                ->where('id', $idPenilaian)
                ->where('tipe', 'pk_umum')
                ->firstOrFail();

            return Excel::download(new PenilaianExport($nilai), 'excelUmum-' . $nilai->nama_karyawan . '.xlsx');
        } catch (\Throwable $th) {

            return dd($th->getMessage());

            // abort(404, 'MAAF TIDAK ADA DATA');
        }
    }
}
