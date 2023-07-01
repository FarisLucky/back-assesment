<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMTipeRequest;
use App\Http\Requests\UpdateMTipeRequest;
use App\Http\Resources\Api\MTipeResource;
use App\Models\MPenilaian;
use App\Models\MTipe;
use App\Models\MTipePenilaian;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class MTipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = request('per_page');
        $columnKeyFilter = request('column_key');
        $columnValFilter = request('column_val');
        $sortBy = request('sort_by');
        $sortType = request('sort_type');

        $tipes = MTipe::query();

        $tipes->when(!is_null($columnKeyFilter) && !is_null($columnValFilter), function ($query) use ($columnKeyFilter, $columnValFilter) {
            for ($i = 0; $i < count($columnKeyFilter); $i++) {
                $query->where($columnKeyFilter[$i], 'LIKE', "%{$columnValFilter[$i]}%");
            }
        });

        $tipes->when(!is_null($sortBy) && !is_null($sortType), function ($query) use ($sortBy, $sortType) {
            $query->orderBy($sortBy, $sortType);
        }, function ($query) {
            $query->orderBy('id', 'desc');
        });

        return MTipeResource::collection($tipes->latest()->paginate($page));
    }

    public function data()
    {
        $tipe = MTipe::leftJoin('m_tipe_penilaian', 'm_tipe.id', '=', 'm_tipe_penilaian.id_tipe')
            ->select(
                DB::raw('m_tipe.id'),
                DB::raw('m_tipe.nama'),
                DB::raw('m_tipe.tipe'),
            )
            ->whereNull('m_tipe_penilaian.id_tipe')
            ->get();

        return MTipeResource::collection($tipe);
    }

    public function tampilByTipe($tipe)
    {
        try {
            if (!in_array($tipe, MPenilaian::TIPE)) {
                throw new Exception('TIPE YANG DIINPUTKAN TIDAK VALID');
            }
            DB::statement("SET SQL_MODE=''"); //this is the trick use it just before your query (untuk menghilangkah only_full_group_by)

            $tipe = MTipePenilaian::with('tipePenilaianByTipe.jabatan', 'tipe')
                ->where('tipe', $tipe)
                ->groupBy('id_tipe')
                ->get();

            $penilaianByTipe = $tipe->map(function ($penilaian) {
                $penilaian->jabatan = $penilaian->tipePenilaianByTipe->map->jabatan->map(function ($jabatan) {
                    return (object) [
                        'id' => $jabatan->id,
                        'nama' => $jabatan->nama,
                    ];
                });

                unset($penilaian->tipePenilaianByTipe); // remove penilaian jabatan val

                return $penilaian;
            });

            return response()->json($penilaianByTipe);
        } catch (\Throwable $th) {

            return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMTipeRequest $request)
    {
        try {
            $data = $request->validated();

            MTipe::create($data);

            return response()->json('Tindakan Berhasil', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MTipe  $mTipe
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mTipe = MTipe::findOrFail($id);

        return response()->json(
            new MTipeResource($mTipe),
            Response::HTTP_OK
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MTipe  $mTipe
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMTipeRequest $request, $id)
    {
        $mTipe = MTipe::findOrFail($id);
        $mTipe->update($request->validated());

        return response()->json(
            'Tindakan Berhasil',
            Response::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MTipe  $mTipe
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $mTipe = MTipe::findOrFail($id);
        $mTipe->delete();

        return response()->json('', Response::HTTP_NO_CONTENT);
    }
}
