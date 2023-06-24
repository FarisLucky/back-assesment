<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubPenilaianKaryawanRequest;
use App\Http\Requests\UpdateSubPenilaianKaryawanRequest;
use App\Http\Resources\Api\SubPenilaianKaryawanResource;
use App\Models\SubPenilaianKaryawan;
use Illuminate\Http\Response;

class SubPenilaianKaryawanController extends Controller
{
    public function index()
    {
        $page = request('per_page');
        $columnKeyFilter = request('column_key');
        $columnValFilter = request('column_val');
        $sortBy = request('sort_by');
        $sortType = request('sort_type');

        $subPenilaianKaryawans = SubPenilaianKaryawan::query();

        $subPenilaianKaryawans->when(!is_null($columnKeyFilter) && !is_null($columnValFilter), function ($query) use ($columnKeyFilter, $columnValFilter) {
            for ($i = 0; $i < count($columnKeyFilter); $i++) {
                $query->where($columnKeyFilter[$i], 'LIKE', "%{$columnValFilter[$i]}%");
            }
        });

        $subPenilaianKaryawans->when(!is_null($sortBy) && !is_null($sortType), function ($query) use ($sortBy, $sortType) {
            $query->orderBy($sortBy, $sortType);
        }, function ($query) {
            $query->orderBy('id', 'desc');
        });

        return SubPenilaianKaryawanResource::collection($subPenilaianKaryawans->latest()->paginate($page));
    }

    public function data()
    {
        $subPenilaianKaryawans = SubPenilaianKaryawan::latest()->get();

        return SubPenilaianKaryawanResource::collection($subPenilaianKaryawans);
    }

    public function store(StoreSubPenilaianKaryawanRequest $request)
    {
        $data = $request->validated();
        $data += [
            'updated_by' => 1
        ];
        $unit = SubPenilaianKaryawan::create($data);

        return new SubPenilaianKaryawanResource($unit);
    }

    public function show($id)
    {
        $subPenilaianKaryawans = SubPenilaianKaryawan::findOrFail($id);

        return new SubPenilaianKaryawanResource($subPenilaianKaryawans);
    }

    public function update(UpdateSubPenilaianKaryawanRequest $request, $id)
    {
        $subPenilaianKaryawans = SubPenilaianKaryawan::findOrFail($id);
        $subPenilaianKaryawans->update($request->validated());

        return new SubPenilaianKaryawanResource($subPenilaianKaryawans);
    }

    public function destroy($id)
    {
        $subPenilaianKaryawans = SubPenilaianKaryawan::findOrFail($id);
        $subPenilaianKaryawans->delete();

        return response()->json([
            'data' => ''
        ], Response::HTTP_NO_CONTENT);
    }
}
