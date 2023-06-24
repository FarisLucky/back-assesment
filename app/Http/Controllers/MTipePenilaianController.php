<?php

namespace App\Http\Controllers;

use App\Models\MTipePenilaian;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class MTipePenilaianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MTipePenilaian  $mTipePenilaian
     * @return \Illuminate\Http\Response
     */
    public function show(MTipePenilaian $mTipePenilaian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MTipePenilaian  $mTipePenilaian
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //return response()->json($request->all(), 500);
        $penilaianUmum = $request->penilaian_umum;
        $penilaianKhusus = $request->penilaian_khusus;

        $data = [
            'penilaianUmum' => $penilaianUmum,
            'penilaianKhusus' => $penilaianKhusus
        ];
        $dataPenilaian = [];
        $tipe = 'pk_umum';

        foreach ($data as $penilaian) {
            foreach ($penilaian as $p) {
                if (count($p['jabatan']) < 1) {
                    continue;
                }

                foreach ($p['jabatan'] as $jabatan) {
                    array_push($dataPenilaian, [
                        'id_tipe' => $p['id_tipe'],
                        'id_jabatan' => $jabatan['id'],
                        'tipe' => $tipe,
                    ]);
                }
            }
        }

        try {
            DB::beginTransaction();

            foreach ($dataPenilaian as $tipePenilaian) {
                MTipePenilaian::updateOrInsert(
                    $tipePenilaian,
                    $tipePenilaian,
                );
            }

            DB::commit();

            return response()->json('Berhasil ditambahkan', Response::HTTP_CREATED);
        } catch (\Throwable $th) {

            DB::rollback();

            return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MTipePenilaian  $mTipePenilaian
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $jabatan = MTipePenilaian::findOrFail($id);
            $jabatan->delete();

            return response()->json('', Response::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {

            return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
