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
            ->whereIn('id_jabatan', $childJabatan->pluck('id'));

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

        $jabatan = MJabatan::select('id')
            ->where('id_parent', auth()->user()->karyawan->id_jabatan)
            ->get();

        $karyawans = PenilaianKaryawan::with([
            'karyawan' => function ($query) {
                $query->where(function ($query) {
                    $query->whereMonth('created_at', date('m'))
                        ->whereYear('created_at', date('Y'));
                })
                    ->whereNotNull('validasi_by');
            }
        ])
            ->where('id', '<>', auth()->user()->id_karyawan);

        $karyawans->when(!is_null($jabatan), function ($query) use ($jabatan) {
            $query->whereIn('id_jabatan', $jabatan->pluck('id'));
        });

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

        // $idJabatanPenilai = auth()->user()->karyawan->id_jabatan;
        $idJabatanPenilai = 168;

        $karyawan = MKaryawan::where('id', $idKaryawan)->firstOrFail();

        $idJabatanKinerja = $karyawan->id_jabatan;

        $mPenilaian = MTipe::with([
            'penilaian',
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

        $mPenilaian->transform(function ($tipe) use ($idJabatanPenilai, $getTipePenilaian) {
            $tipe->check = $getTipePenilaian->where('id_jabatan', $idJabatanPenilai)
                ->where('id_tipe', $tipe->id)
                ->count();

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

            foreach ($input['penilaians'] as $tipePenilaian) {

                $tipePenilaianData = [
                    'id_pk' => $storePenilaian->id,
                    'nama_tipe' => $tipePenilaian['nama'],
                    'tipe_pk' => $tipePenilaian['tipe'],
                    'catatan' => optional($tipePenilaian)['catatan'],
                    'id_karyawan' => auth()->user()->karyawan->id,
                    'nama_penilai' => auth()->user()->karyawan->nama,
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
                        'nama_penilai' => $penilai->nama,
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
                }
            }

            $storePenilaian->ttl_nilai = $params['penilaian_ttl'];
            if ($params['penilaian_ttl'] > 0) {
                $storePenilaian->rata_nilai = $params['penilaian_ttl'] / $params['jml_indikator']; // rata rata nilai => ttl_nilai dibagi banyak indikator
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
            $data['data']['status'] = Response::HTTP_CREATED;
        } catch (\Throwable $th) {

            DB::rollBack();

            $data['data']['message'] = $th->getMessage();
            $data['data']['status'] = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return response()->json($data, $data['data']['status']);
    }

    public function show($id)
    {
        $penilaianKaryawans = PenilaianKaryawan::findOrFail($id);

        return new PenilaianKaryawanResource($penilaianKaryawans);
    }

    public function update(UpdatePenilaianKaryawanRequest $request, $id)
    {
        $penilaianKaryawans = PenilaianKaryawan::findOrFail($id);
        $penilaianKaryawans->update($request->validated());

        return new PenilaianKaryawanResource($penilaianKaryawans);
    }

    public function destroy($id)
    {
        $penilaianKaryawans = PenilaianKaryawan::findOrFail($id);
        $penilaianKaryawans->delete();

        return response()->json('', Response::HTTP_NO_CONTENT);
    }
}
