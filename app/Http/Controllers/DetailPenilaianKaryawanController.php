<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDetailPenilaianRequest;
use App\Http\Requests\UpdateDetailPenilaianRequest;
use App\Http\Resources\Api\DetailPenilaianResource;
use App\Models\DetailPenilaian;
use Illuminate\Http\Response;

class DetailPenilaianKaryawanController extends Controller
{
    public function index()
    {
        $page = request('per_page');
        $columnKeyFilter = request('column_key');
        $columnValFilter = request('column_val');
        $sortBy = request('sort_by');
        $sortType = request('sort_type');

        $detailPenilaians = DetailPenilaian::query();

        $detailPenilaians->when(!is_null($columnKeyFilter) && !is_null($columnValFilter), function ($query) use ($columnKeyFilter, $columnValFilter) {
            for ($i = 0; $i < count($columnKeyFilter); $i++) {
                $query->where($columnKeyFilter[$i], 'LIKE', "%{$columnValFilter[$i]}%");
            }
        });

        $detailPenilaians->when(
            !is_null($sortBy) && !is_null($sortType),
            function ($query) use ($sortBy, $sortType) {
                $query->orderBy($sortBy, $sortType);
            },
            function ($query) {
                $query->orderBy('id', 'desc');
            }
        );

        return DetailPenilaianResource::collection(
            $detailPenilaians->latest()->paginate($page)
        );
    }

    public function data()
    {
        $detailPenilaians = DetailPenilaian::latest()->get();

        return DetailPenilaianResource::collection($detailPenilaians);
    }

    public function store(StoreDetailPenilaianRequest $request)
    {
        $data = $request->validated();
        $data += [
            'updated_by' => 1
        ];
        $unit = DetailPenilaian::create($data);

        return new DetailPenilaianResource($unit);
    }

    public function show($id)
    {
        $detailPenilaians = DetailPenilaian::findOrFail($id);

        return new DetailPenilaianResource($detailPenilaians);
    }

    public function update(UpdateDetailPenilaianRequest $request, $id)
    {
        $detailPenilaians = DetailPenilaian::findOrFail($id);
        $detailPenilaians->update($request->validated());

        return new DetailPenilaianResource($detailPenilaians);
    }

    public function destroy($id)
    {
        $detailPenilaians = DetailPenilaian::findOrFail($id);
        $detailPenilaians->delete();

        return response()->json([
            'data' => ''
        ], Response::HTTP_NO_CONTENT);
    }
}
