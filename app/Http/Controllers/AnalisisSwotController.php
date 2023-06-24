<?php

namespace App\Http\Controllers;

use App\Models\AnalisisSwot;
use App\Http\Requests\StoreAnalisisSwotRequest;
use App\Http\Requests\UpdateAnalisisSwotRequest;
use App\Http\Resources\Api\AnalisisSwotResource;
use Illuminate\Http\Response;

class AnalisisSwotController extends Controller
{
    public function index()
    {
        $page = request('per_page');
        $columnKeyFilter = request('column_key');
        $columnValFilter = request('column_val');
        $sortBy = request('sort_by');
        $sortType = request('sort_type');

        $swots = AnalisisSwot::query();

        $swots->when(!is_null($columnKeyFilter) && !is_null($columnValFilter), function ($query) use ($columnKeyFilter, $columnValFilter) {
            for ($i = 0; $i < count($columnKeyFilter); $i++) {
                $query->where($columnKeyFilter[$i], 'LIKE', "%{$columnValFilter[$i]}%");
            }
        });

        $swots->when(!is_null($sortBy) && !is_null($sortType), function ($query) use ($sortBy, $sortType) {
            $query->orderBy($sortBy, $sortType);
        }, function ($query) {
            $query->orderBy('id', 'desc');
        });

        return AnalisisSwotResource::collection(
            $swots->latest()->paginate($page)
        );
    }

    public function data()
    {
        $units = AnalisisSwot::latest()->get();

        return AnalisisSwotResource::collection($units);
    }

    public function store(StoreAnalisisSwotRequest $request)
    {
        $swot = AnalisisSwot::create($request->validated());

        return response()->json(
            new AnalisisSwotResource($swot),
            Response::HTTP_OK
        );
    }

    public function show($id)
    {
        $swot = AnalisisSwot::findOrFail($id);

        return response()->json(
            new AnalisisSwotResource($swot),
            Response::HTTP_OK
        );
    }

    public function update(UpdateAnalisisSwotRequest $request, $id)
    {
        $swot = AnalisisSwot::findOrFail($id);
        $swot->update($request->validated());

        return response()->json(
            new AnalisisSwotResource($swot),
            Response::HTTP_OK
        );
    }

    public function destroy($id)
    {

        $swot = AnalisisSwot::findOrFail($id);
        $swot->delete();

        return response()->json('', Response::HTTP_NO_CONTENT);
    }
}
