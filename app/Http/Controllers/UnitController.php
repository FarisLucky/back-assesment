<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Http\Resources\Api\UnitResource;
use App\Models\MUnit;
use Illuminate\Http\Response;

class UnitController extends Controller
{
    public function index()
    {
        $page = request('per_page');
        $columnKeyFilter = request('column_key');
        $columnValFilter = request('column_val');
        $sortBy = request('sort_by');
        $sortType = request('sort_type');

        $units = MUnit::query();

        $units->when(!is_null($columnKeyFilter) && !is_null($columnValFilter), function ($query) use ($columnKeyFilter, $columnValFilter) {
            for ($i = 0; $i < count($columnKeyFilter); $i++) {
                $query->where($columnKeyFilter[$i], 'LIKE', "%{$columnValFilter[$i]}%");
            }
        });

        $units->when(!is_null($sortBy) && !is_null($sortType), function ($query) use ($sortBy, $sortType) {
            $query->orderBy($sortBy, $sortType);
        }, function ($query) {
            $query->orderBy('id', 'desc');
        });

        return UnitResource::collection($units->latest()->paginate($page));
    }

    public function data()
    {
        $units = MUnit::latest()->get();

        return UnitResource::collection($units);
    }

    public function store(StoreUnitRequest $request)
    {
        $unit = MUnit::create($request->validated());

        return response()->json(
            'Tindakan Berhasil',
            Response::HTTP_OK
        );
    }

    public function show($id)
    {
        $unit = MUnit::findOrFail($id);

        return response()->json(
            new UnitResource($unit),
            Response::HTTP_OK
        );
    }

    public function update(UpdateUnitRequest $request, $id)
    {
        $unit = MUnit::findOrFail($id);
        $unit->update($request->validated());

        return response()->json(
            'Tindakan Berhasil',
            Response::HTTP_OK
        );
    }

    public function destroy($id)
    {
        $unit = MUnit::findOrFail($id);
        $unit->delete();

        return response()->json('', Response::HTTP_NO_CONTENT);
    }
}
