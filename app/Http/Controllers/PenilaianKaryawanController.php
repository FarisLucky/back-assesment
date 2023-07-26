<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SearchingTrait;
use App\Models\PenilaianKaryawan;
use App\Models\MPenilaian;
use App\Http\Requests\StorePenilaianKaryawanRequest;
use App\Http\Requests\UpdatePenilaianKaryawanRequest;
use App\Http\Resources\Api\MKaryawanResource;
use App\Http\Resources\Api\MTipeResource;
use App\Http\Resources\Api\PenilaianKaryawanResource;
use App\Models\AnalisisSwot;
use App\Models\Comment;
use App\Models\DetailPenilaian;
use App\Models\MJabatan;
use App\Models\MKaryawan;
use App\Models\MTipe;
use App\Models\MTipePenilaian;
use App\Models\SubPenilaianKaryawan;
use App\Models\TipePenilaian;
use App\Services\PenilaianKaryawanServices;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PenilaianKaryawanController extends Controller
{
    use SearchingTrait;

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
            ->whereHas('jabatan', function ($query) use ($karyawan) {
                $jabatan = $karyawan->karyawan->jabatan;
                // if ($jabatan->level == 4) { // kepala staff
                $query->where('id_parent', $jabatan->id);
                // }
                $query->where('kategori', $jabatan->kategori);
            })
            ->where('id', '<>', $karyawan->id_karyawan);

        $relations = Arr::get(config('relationship'), MKaryawan::class);
        $penilaianKaryawans->when(!is_null($columnKeyFilter) && !is_null($columnValFilter), function ($query) use ($columnKeyFilter, $columnValFilter, $relations) {
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

        $karyawan = auth()->user();

        DB::statement("SET SQL_MODE=''"); //this is the trick use it just before your query (untuk menghilangkah only_full_group_by)
        $penilaians = PenilaianKaryawan::with('karyawan')
            ->select('id', 'id_karyawan', 'jabatan', 'tipe', 'tgl_nilai', 'status')
            // ->where(function ($query) use ($year, $month) {
            //     $query->whereMonth('tgl_nilai', $month)
            //         ->whereYear('tgl_nilai', $year);
            // })
            ->whereHas('karyawan.jabatan', function ($query) use ($karyawan) {
                if ($karyawan->role != 'ADMIN') {
                    $jabatan = $karyawan->karyawan->jabatan;
                    $query->where('kategori', $jabatan->kategori);
                }
            })
            ->whereNull('validasi_by')
            ->where('id_karyawan', '<>', $karyawan->id_karyawan);

        $relations = Arr::get(config('relationship'), MKaryawan::class);

        $penilaians->when(!is_null($columnKeyFilter) && !is_null($columnValFilter), function ($query) use ($columnKeyFilter, $columnValFilter, $relations) {
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

        $penilaians->when(!is_null($sortBy) && !is_null($sortType), function ($query) use ($sortBy, $sortType) {
            $query->orderBy($sortBy, $sortType);
        }, function ($query) {
            $query->orderBy('id', 'desc');
        });

        return PenilaianKaryawanResource::collection(
            $penilaians->latest()->paginate($page)
        );
    }

    public function getNilai($idKaryawan, $tipe)
    {

        $karyawan = MKaryawan::where('id', $idKaryawan)->firstOrFail();

        $parent = $karyawan->load('jabatan');

        $idJabatanPenilai = auth()->user()->karyawan->id_jabatan;

        $mPenilaian = MTipe::with([
            'penilaian' => function ($query) {
                $query->whereHas('subPenilaian')
                    ->orderBy('order');
            },
            'penilaian.subPenilaian' => function ($query) use ($tipe, $idJabatanPenilai) {

                $query->select('id', 'id_penilaian', 'nama');
                if ($tipe == MPenilaian::TIPE[1]) {
                    $query->whereHas('mValidasiPenilai', function ($query) use ($idJabatanPenilai) {
                        $query->where('id_jabatan_penilai', $idJabatanPenilai)
                            ->orWhereNull('id_jabatan_penilai');
                    });
                }
            }
        ])
            ->select('id', 'nama', 'tipe')
            ->whereHas('penilaian.subPenilaian')
            ->where('tipe', $tipe)
            ->get();

        $mPenilaian->when($tipe == MPenilaian::TIPE[1]);

        $getTipePenilaian = MTipePenilaian::all(['id_tipe', 'id_jabatan']);

        $tipeParams = $tipe;

        $mPenilaian->transform(function ($tipe) use ($idJabatanPenilai, $parent, $getTipePenilaian, $tipeParams) {

            if ($tipeParams == MPenilaian::TIPE[0]) {
                $tipe->check = $getTipePenilaian->where('id_jabatan', $idJabatanPenilai)
                    ->where('id_tipe', $tipe->id)
                    ->count();
                $idTipeKepalaStaff = 7; // ambil dari database

                if (
                    $tipe->id == $idTipeKepalaStaff // dan apabila id tipe bukan penilaian id penilaian kepala staff
                    && $parent->jabatan->id_parent != $idJabatanPenilai // jika yang menilai bukan kepala staff nya
                ) { // tipe kepala staff
                    $tipe->check = 0;
                }
            } else if ($tipeParams == MPenilaian::TIPE[1] && $parent->jabatan->id_parent == $idJabatanPenilai) {

                $tipe->check = 1;
            } else {
                $tipe->check = 0;
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
            $tglNilai = date('Y-m-d', mktime(0, 0, 0, $request->bulan_nilai, date('d'), $request->tahun_nilai));

            $getPenilaian = PenilaianKaryawan::where([
                'id_karyawan' => $request->id_karyawan,
                'tipe' => $request->tipe,
            ])
                ->whereMonth('created_at', $request->bulan_nilai)
                ->whereYear('created_at', $request->tahun_nilai)
                ->first();

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

            $kategori = $userPenilai->karyawan->jabatan->kategori;

            $penilaianData =  [ // setup data penilaian
                'id_karyawan' => $karyawan->id,
                'nama_karyawan' => $karyawan->nama,
                'jabatan' => $karyawan->jabatan->nama,
                'id_penilai' => $penilai->id,
                'nama_penilai' => $penilai->nama,
                'jabatan_penilai' => $penilai->jabatan->nama,
                'tgl_nilai' => $tglNilai,
                'ttl_nilai' => 0,
                'rata_nilai' => 0,
                'tipe' => $input['tipe'],
                'status' => PenilaianKaryawan::STATUS[1],
                'validasi_by' => null,
                'kategori' => $kategori,
                'created_by' => $karyawan->id,
                'updated_by' => $karyawan->id,
            ];

            $storePenilaian = PenilaianKaryawan::create($penilaianData);

            $ttlRataNilai = 0;
            $countNilai = 0;
            $rataNilai = 0;

            foreach ($input['penilaians'] as $tipePenilaian) {

                $getTipePenilaian = MTipePenilaian::where([
                    'id_tipe' => $tipePenilaian['id'],
                    'kategori' => $penilai->jabatan->kategori,
                ])->first();

                $karyawanPenilai = null;

                if (!is_null(optional($getTipePenilaian)->id_jabatan)) {
                    $karyawanPenilai = MKaryawan::where('id_jabatan', $getTipePenilaian->id_jabatan)->first();
                }

                $tipePenilaianData = [
                    'id_pk' => $storePenilaian->id,
                    'id_tipe' => $tipePenilaian['id'],
                    'nama_tipe' => $tipePenilaian['nama'],
                    'tipe_pk' => $tipePenilaian['tipe'],
                    'catatan' => optional($tipePenilaian)['catatan'],
                    'id_karyawan' => $tipePenilaian['check'] > 0 ? $penilai->id : optional($karyawanPenilai)->id,
                    'nama_penilai' => $tipePenilaian['check'] > 0 ? $penilai->nama : null,
                ];

                $storeTipePenilaian = TipePenilaian::create($tipePenilaianData);

                foreach ($tipePenilaian['relationship']['m_penilaian'] as $detail) {
                    $rataNilai = 0;

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

                    if (
                        $kategori == MJabatan::MEDIS
                        && $input['tipe'] == MPenilaian::TIPE[1] // jika tipe penilaian khusus
                    ) { // jika penilaian medis dan khusus
                        $ttlMedis = $penilaianServices->hitungMedis([
                            'bobot' => $detail['bobot'],
                            'ttlTipe' => $params['detail_ttl'],
                        ]);

                        $rataNilai = $ttlMedis['cVal'];
                        $params['detail_ttl'] = $ttlMedis['dVal'];
                    }

                    $detailData = [
                        'id_pk' => $storePenilaian->id,
                        'id_tipe_pk' => $storeTipePenilaian->id,
                        'nama_penilaian' => $detail['nama'],
                        'ttl_nilai' => $params['detail_ttl'],
                        'rata_nilai' => $rataNilai,
                        'id_penilai' => $penilai->id,
                        'bobot' => $detail['bobot'],
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

            if ($input['tipe'] == MPenilaian::TIPE[1]) { // jika penilaian khusus
                Comment::create([
                    'id_pk' => $storePenilaian->id,
                    'catatan' => $request->comment['catatan'],
                    'penilai' => $request->comment['penilai'],
                    'dinilai' => $request->comment['dinilai'],
                    'ancaman' => $request->analisis_swot['ancaman'],
                ]);
            } else {
                AnalisisSwot::create([
                    'id_pk' => $storePenilaian->id,
                    'kelebihan' => $request->analisis_swot['kelebihan'],
                    'kekurangan' => $request->analisis_swot['kekurangan'],
                    'kesempatan' => $request->analisis_swot['kesempatan'],
                    'ancaman' => $request->analisis_swot['ancaman'],
                ]);
            }

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

            $karyawan = auth()->user()->karyawan;

            $penilaian = PenilaianKaryawan::with([
                // 'karyawan.jabatan', // tipe penilaian relationship
                'tipePenilaian', // tipe penilaian relationship
                'comment', // tipe penilaian relationship
                'tipePenilaian.detailPenilaian', // tipe penilaian relationship
                'tipePenilaian.detailPenilaian.subPenilaian', // tipe penilaian relationship
                'analisisSwot', // analisis swot relationship
            ])
                ->where('id', $id)
                ->first();

            if (is_null($penilaian)) {
                throw new ModelNotFoundException('Maaf penilaian belum ada !');
            }

            $getTipePenilaian = MTipePenilaian::all(['id_tipe', 'id_jabatan']);
            $tipeParam = $penilaian->tipe;

            $penilaian->tipePenilaian->transform(function ($tipe) use ($getTipePenilaian, $tipeParam, $karyawan) {

                if ($tipeParam == MPenilaian::TIPE[0] && is_null($tipe->id_karyawan)) {
                    $tipe->check_penilai = $getTipePenilaian->where('id_jabatan', $karyawan->id_jabatan)
                        ->where('id_tipe', $tipe->id_tipe)
                        ->count();
                } else {
                    $tipe->check_penilai = $karyawan->id == $tipe->id_karyawan ? 2 : 0;
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

            $penilaianServices = new PenilaianKaryawanServices();

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
                        if ($getPenilaian->kategori == MJabatan::MEDIS && $getPenilaian->tipe == MPenilaian::TIPE[1]) {
                            $hitung = $penilaianServices->hitungMedis([
                                'bobot' => $detail['bobot'],
                                'ttlTipe' => $sumDetail,
                            ]);
                            $avgDetail = $hitung['cVal'];
                            $sumDetail = $hitung['dVal'];
                        } else {
                            $avgDetail = $sumDetail / count($detail['relationship']['sub']);
                        }

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

            if ($getPenilaian->tipe == MPenilaian::TIPE[1]) { // jika penilaian khusus
                $comment = Comment::where('id_pk', $getPenilaian->id)->first();
                $comment->catatan = $request->comment['catatan'];
                $comment->penilai = $request->comment['penilai'];
                $comment->dinilai = $request->comment['dinilai'];
                $comment->save();
            } else {
                AnalisisSwot::where('id_pk', $getPenilaian->id)
                    ->update([
                        'kelebihan' => $request->analisis_swot['kelebihan'],
                        'kekurangan' => $request->analisis_swot['kekurangan'],
                        'kesempatan' => $request->analisis_swot['kesempatan'],
                        'ancaman' => $request->analisis_swot['ancaman'],
                    ]);
            }

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

    public function validasiNilai(Request $request)
    {
        try {

            $penilaian = PenilaianKaryawan::with('tipePenilaian')
                ->find($request->id);
            foreach ($penilaian->tipePenilaian as $tipe) {
                if (is_null($tipe->id_karyawan)) {
                    throw new Exception('Silahkan lengkapi penilaian');
                }
            }
            $penilaian->validasi_by = auth()->user()->id;
            $penilaian->save();

            return response()->json(new PenilaianKaryawanResource($penilaian));
        } catch (\Throwable $th) {

            $data = [
                'message' => $th->getMessage()
            ];

            return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
