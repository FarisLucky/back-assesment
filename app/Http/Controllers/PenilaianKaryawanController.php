<?php

namespace App\Http\Controllers;

use App\Models\PenilaianKaryawan;
use App\Models\MPenilaian;
use App\Http\Requests\StorePenilaianKaryawanRequest;
use App\Http\Requests\UpdatePenilaianKaryawanRequest;
use App\Http\Resources\Api\MKaryawanResource;
use App\Http\Resources\Api\MTipeResource;
use App\Http\Resources\Api\PenilaianKaryawanResource;
use App\Models\AnalisisSwot;
use App\Models\DetailPenilaian;
use App\Models\MJabatan;
use App\Models\MKaryawan;
use App\Models\MTipe;
use App\Models\MTipePenilaian;
use App\Models\SubPenilaianKaryawan;
use App\Models\TipePenilaian;
use App\Models\User;
use App\Services\PenilaianKaryawanServices;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PenilaianKaryawanController extends Controller
{
    public function index()
    {
        $page = request('per_page');
        $columnKeyFilter = request('column_key');
        $columnValFilter = request('column_val');
        $sortBy = request('sort_by');
        $sortType = request('sort_type');
        $month = date('m');
        $year = date('Y');

        $karyawan = auth()->user();

        $childJabatan = MJabatan::select('id', 'id_parent')
            ->where('id_parent', $karyawan->karyawan->id_jabatan)
            ->get();

        $penilaianKaryawans = MKaryawan::with([
            'jabatan',
            'penilaianKaryawanKhusus' => function ($query) use ($month, $year) {
                $query->select('id', 'id_karyawan', 'tipe', 'tgl_nilai', 'status')
                    ->whereMonth('tgl_nilai', $month)
                    ->whereYear('tgl_nilai', $year);
            },
            'penilaianKaryawanUmum' => function ($query) use ($month, $year) {
                $query->select('id', 'id_karyawan', 'tipe', 'tgl_nilai', 'status')
                    ->whereMonth('tgl_nilai', $month)
                    ->whereYear('tgl_nilai', $year);
            },
        ])
            ->where('id', '<>', $karyawan->id_karyawan);

        // $penilaianKaryawans->when($childJabatan->isNotEmpty(), function ($query) use ($childJabatan) {
        //     $query->whereIn('id_jabatan', $childJabatan->pluck('id'));
        // });

        // $penilaianKaryawans->when($childJabatan->isEmpty(), function ($query) use ($karyawan) {
        //     $query->where('id_unit', $karyawan->karyawan->id_unit)
        //         ->where('id_jabatan', $karyawan->karyawan->id_jabatan);
        // });

        $penilaianKaryawans->when(!is_null($columnKeyFilter) && !is_null($columnValFilter), function ($query) use ($columnKeyFilter, $columnValFilter) {
            for ($i = 0; $i < count($columnKeyFilter); $i++) {
                $query->where($columnKeyFilter[$i], 'LIKE', "%{$columnValFilter[$i]}%");
            }
        });

        $penilaianKaryawans->when(!is_null($sortBy) && !is_null($sortType), function ($query) use ($sortBy, $sortType) {
            $query->orderBy($sortBy, $sortType);
        }, function ($query) {
            $query->orderBy('id', 'desc');
        });

        return MKaryawanResource::collection($penilaianKaryawans->latest()->paginate($page));
    }

    public function indexProgress()
    {
        $page = request('per_page');
        $columnKeyFilter = request('column_key');
        $columnValFilter = request('column_val');
        $sortBy = request('sort_by');
        $sortType = request('sort_type');
        $month = date('m');
        $year = date('Y');

        $karyawan = User::with('karyawan')->find(1);

        $childJabatan = MJabatan::select('id')
            ->where('id_parent', $karyawan->karyawan->id_jabatan)
            ->get();

        $karyawans = MKaryawan::withWhereHas(
            'penilaianKaryawan',
            function ($query) use ($month, $year) {
                $query->select('id', 'id_karyawan', 'tipe', 'tgl_nilai', 'status')
                    ->whereMonth('tgl_nilai', $month)
                    ->whereYear('tgl_nilai', $year);
            }
        )
            ->where('id', '<>', $karyawan->id_karyawan);

        // $penilaianKaryawans->when($childJabatan->isNotEmpty(), function ($query) use ($childJabatan) {
        //     $query->whereIn('id_jabatan', $childJabatan->pluck('id'));
        // });

        // $penilaianKaryawans->when($childJabatan->isEmpty(), function ($query) use ($karyawan) {
        //     $query->where('id_unit', $karyawan->karyawan->id_unit)
        //         ->where('id_jabatan', $karyawan->karyawan->id_jabatan);
        // });

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

    public function getNilai($idKaryawan, $tipe)
    {

        $karyawan = MKaryawan::where('id', $idKaryawan)->firstOrFail();

        $idJabatanPenilai = auth()->user()->karyawan->id_jabatan;
        $idJabatanKinerja = $karyawan->id_jabatan;

        $mPenilaian = MTipe::with([
            'penilaian' => function ($query) {
                $query->whereHas('subPenilaian');
            },
            'penilaian.subPenilaian' => function ($query) use ($tipe, $idJabatanPenilai, $idJabatanKinerja) {

                $query->select('id', 'id_penilaian', 'nama');
                $query->when($tipe == MPenilaian::TIPE[1], function ($query) use ($idJabatanPenilai, $idJabatanKinerja) {

                    $query->where('id_jabatan_penilai', $idJabatanPenilai)
                        ->where('id_jabatan_kinerja', $idJabatanKinerja);
                });
            }
        ])
            ->select('id', 'nama', 'tipe')
            ->whereHas('penilaian.subPenilaian')
            ->where('tipe', $tipe)
            ->get();

        $getTipePenilaian = MTipePenilaian::all(['id_tipe', 'id_jabatan']);

        $tipeParams = $tipe;

        $mPenilaian->transform(function ($tipe) use ($idJabatanPenilai, $getTipePenilaian, $tipeParams) {

            if ($tipeParams == MPenilaian::TIPE[0]) {
                $tipe->check = $getTipePenilaian->where('id_jabatan', $idJabatanPenilai)
                    ->where('id_tipe', $tipe->id)
                    ->count();
            } else {
                $tipe->check = true;
            }

            $tipe->penilaian = $tipe->penilaian->map(function ($nilai) {
                $nilai->sub_count = $nilai->subPenilaian->count();

                return $nilai;
            });

            return $tipe;
        });


        return MTipeResource::collection($mPenilaian);
    }

    public function data()
    {
        $penilaianKaryawans = PenilaianKaryawan::latest()->get();

        return PenilaianKaryawanResource::collection($penilaianKaryawans);
    }

    public function store(StorePenilaianKaryawanRequest $request)
    {
        $data = [];

        try {

            $getPenilaian = PenilaianKaryawan::where([
                'id_karyawan' => $request->id_karyawan,
                'tipe' => $request->tipe,
            ])->whereMonth('created_at', date('Y-m-d'))->first();

            if (!is_null($getPenilaian)) {
                throw new Exception('Penilaian Sudah Ada');
            }

            DB::beginTransaction(); // transaction start

            $input = $request->validated();

            $penilaianServices = new PenilaianKaryawanServices();

            $userPenilai = auth()->user();

            $karyawan = $penilaianServices->getKaryawan($input['id_karyawan']);
            $penilai = $penilaianServices->getKaryawan($userPenilai->id_karyawan);

            $params = [
                'penilaian_ttl' => 0,
                'jml_indikator' => 0,
                'updated_by' => 3
            ];

            // return response()->json(var_dump($input));

            $penilaianData =  [ // setup data penilaian
                'id_karyawan' => $karyawan->id,
                'nama_karyawan' => $karyawan->nama,
                'jabatan' => $karyawan->jabatan->nama,
                'id_penilai' => $penilai->id,
                'nama_penilai' => $penilai->nama,
                'jabatan_penilai' => $penilai->jabatan->nama,
                'tgl_nilai' => date('Y-m-d'),
                'ttl_nilai' => 0,
                'rata_nilai' => 0,
                'tipe' => $input['tipe'],
                'status' => PenilaianKaryawan::STATUS[1],
                'validasi_by' => null,
                'created_by' => $karyawan->id,
                'updated_by' => $karyawan->id,
            ];

            $storePenilaian = PenilaianKaryawan::create($penilaianData);

            // throw new HttpException(500, $penilai->id);
            $ttlRataNilai = 0;
            $countNilai = 0;
            $rataNilai = 0;

            foreach ($input['penilaians'] as $tipePenilaian) {

                $tipePenilaianData = [
                    'id_pk' => $storePenilaian->id,
                    'id_tipe' => $tipePenilaian['id'],
                    'nama_tipe' => $tipePenilaian['nama'],
                    'tipe_pk' => $tipePenilaian['tipe'],
                    'catatan' => optional($tipePenilaian)['catatan'],
                    'id_karyawan' => $penilai->id,
                    'nama_penilai' => $tipePenilaian['check'] > 0 ? $penilai->nama : null,
                ];

                $storeTipePenilaian = TipePenilaian::create($tipePenilaianData);

                foreach ($tipePenilaian['relationship']['m_penilaian'] as $detail) {

                    $params['detail_ttl'] = 0; // init ttl detail penilaian
                    $jumlahIndikator = 0; // init jumlah indikator per detail penilaian
                    $subDetail = [];
                    $detailData = null;

                    foreach ($detail['relationship']['sub_penilaian'] as $subPenilaian) {

                        $nilai = is_null(optional($subPenilaian)['nilai']) ? 0 : $subPenilaian['nilai'];

                        array_push($subDetail, [
                            'id_detail' => null, // set to null karena id_detail belum ada
                            'penilaian' => $detail['nama'],
                            'sub_penilaian' => $subPenilaian['nama'],
                            'nilai' => $nilai,
                            'updated_by' => $params['updated_by'],
                            'created_at' => date('y-m-d'),
                            'updated_at' => date('y-m-d'),
                        ]);

                        $params['detail_ttl'] += intval($nilai); // total nilai sub penilaian
                        $jumlahIndikator++;
                    }

                    $params['jml_indikator'] += $jumlahIndikator; // tambahkan total jumlah semua sub indikator ke detail indikator

                    if ($params['detail_ttl'] > 0) {
                        $rataNilai = $params['detail_ttl'] / $jumlahIndikator;
                    } else {
                        $rataNilai = 0;
                    }

                    $detailData = [
                        'id_pk' => $storePenilaian->id,
                        'id_tipe_pk' => $storeTipePenilaian->id,
                        'nama_penilaian' => $detail['nama'],
                        'ttl_nilai' => $params['detail_ttl'],
                        'rata_nilai' => $rataNilai,
                        'id_penilai' => $penilai->id,
                        'nama_penilai' => $tipePenilaian['check'] > 0 ? $penilai->nama : null,
                        'jabatan_penilai' => $penilai->jabatan->nama,
                        'updated_by' => $params['updated_by']
                    ];

                    $params['penilaian_ttl'] += intval($params['detail_ttl']); // total nilai detail penilaian

                    $storeDetail = DetailPenilaian::create($detailData); // insert detail penilaian

                    $subDetail = array_map(function ($item) use ($storeDetail) { // update di_detail di sub detail penilaian
                        $item['id_detail'] = $storeDetail->id;
                        return $item;
                    }, $subDetail);

                    SubPenilaianKaryawan::insert($subDetail); // insert batch semua sub penilaian pada detail penilaian

                    $ttlRataNilai += $rataNilai;
                    $countNilai++;
                }
            }

            $storePenilaian->ttl_nilai = $ttlRataNilai;
            if ($ttlRataNilai > 0) {
                $storePenilaian->rata_nilai = $ttlRataNilai / $countNilai; // rata rata nilai => ttl_nilai dibagi banyak indikator
            } else {
                $storePenilaian->rata_nilai = 0; // rata rata nilai => ttl_nilai dibagi banyak indikator
            }

            $storePenilaian->save();

            // Analisis Swot
            AnalisisSwot::create([
                'id_pk' => $storePenilaian->id,
                'kelebihan' => $request->analisis_swot['kelebihan'],
                'kekurangan' => $request->analisis_swot['kekurangan'],
                'kesempatan' => $request->analisis_swot['kesempatan'],
                'ancaman' => $request->analisis_swot['ancaman'],
            ]);

            DB::commit();

            $data['data']['message'] = 'Tindakan Berhasil !';
            $data['data']['data'] = $storePenilaian;
            $data['data']['status'] = Response::HTTP_CREATED;
        } catch (\Throwable $th) {

            DB::rollBack();

            $data['data']['message'] = $th->getMessage();
            $data['data']['status'] = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return response()->json($data, $data['data']['status']);
    }

    public function showProgress($id)
    {
        try {

            // $idJabatanPenilai = User::find(19)->karyawan->id_jabatan;
            $idJabatanPenilai = auth()->user()->karyawan->id_jabatan;

            $penilaian = PenilaianKaryawan::with([
                'tipePenilaian', // tipe penilaian relationship
                'tipePenilaian.detailPenilaian', // tipe penilaian relationship
                'tipePenilaian.detailPenilaian.subPenilaian', // tipe penilaian relationship
                'analisisSwot', // analisis swot relationship
            ])
                ->where('id', $id)
                ->firstOrFail();

            $getTipePenilaian = MTipePenilaian::all(['id_tipe', 'id_jabatan']);

            $tipeParams = $penilaian->tipe;

            $penilaian->tipePenilaian->transform(function ($tipe) use ($idJabatanPenilai, $getTipePenilaian, $tipeParams) {
                if ($tipeParams == MPenilaian::TIPE[0]) {
                    $tipe->check_penilai = $getTipePenilaian->where('id_jabatan', $idJabatanPenilai)
                        ->where('id_tipe', $tipe->id_tipe)
                        ->count();
                } else {
                    $tipe->check_penilai = true;
                }

                return $tipe;
            });

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

    public function update(UpdatePenilaianKaryawanRequest $request, $id)
    {

        $data = [];

        try {

            DB::beginTransaction(); // transaction start

            $input = $request->validated();

            $getPenilaian = PenilaianKaryawan::find($id);

            if (is_null($getPenilaian)) {
                throw new Exception('Penilaian Belum Ada');
            }

            $countTipe = 0;

            $ttlRataNilai = 0;

            foreach ($input['penilaian']['relationship']['tipe_penilaian'] as $tipe) {

                if (intval($tipe['check_penilai']) > 0) {

                    foreach ($tipe['relationship']['detail'] as $detail) {
                        $avgDetail = 0;
                        $sumDetail = 0;

                        foreach ($detail['relationship']['sub'] as $sub) {

                            $sumDetail += intval($sub['nilai']);
                            $getSub = SubPenilaianKaryawan::find($sub['id']);
                            $getSub->nilai = $sub['nilai'];
                            $getSub->save();
                        }
                        // Average Detail Penilaian
                        $avgDetail = $sumDetail / count($detail['relationship']['sub']);

                        $getDetail = DetailPenilaian::find($detail['id']);
                        $getDetail->ttl_nilai = $sumDetail;
                        $getDetail->rata_nilai = $avgDetail;
                        $getDetail->save();
                    }

                    $userPenilai = auth()->user();

                    $tipePenilaian = TipePenilaian::find($tipe['id']);
                    $tipePenilaian->catatan = optional($tipe)['catatan'];
                    $tipePenilaian->id_karyawan = $userPenilai->karyawan->id;
                    $tipePenilaian->nama_penilai = $userPenilai->karyawan->nama;
                    $tipePenilaian->save();
                }
            }

            $details = DetailPenilaian::select('id_pk', 'rata_nilai')->where('id_pk', $getPenilaian->id)->get();
            $countTipe = $details->count();

            $details->each(function ($detail) use (&$ttlRataNilai) {
                $ttlRataNilai += $detail->rata_nilai;
            });

            $getPenilaian->ttl_nilai = $ttlRataNilai;
            $getPenilaian->rata_nilai = $ttlRataNilai / $countTipe;
            $getPenilaian->save();

            // Analisis Swot;
            // $swot = AnalisisSwot::find($getPenilaian->id);
            // $swot->kelebihan = $request->analisis_swot['kelebihan'];
            // $swot->kekurangan = $request->analisis_swot['kekurangan'];
            // $swot->kesempatan = $request->analisis_swot['kesempatan'];
            // $swot->ancaman = $request->analisis_swot['ancaman'];
            // $swot->save();
            // throw new Exception('test');

            DB::commit();

            $data['data']['message'] = 'Tindakan Berhasil !';
            $data['data']['status'] = Response::HTTP_OK;
        } catch (\Throwable $th) {

            DB::rollBack();

            $data['data']['message'] = $th->getMessage();
            $data['data']['status'] = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return response()->json($data, $data['data']['status']);
    }

    public function destroy($id)
    {
        $penilaianKaryawans = PenilaianKaryawan::findOrFail($id);
        $penilaianKaryawans->delete();

        return response()->json('', Response::HTTP_NO_CONTENT);
    }
}
