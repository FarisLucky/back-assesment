<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJabatanRequest;
use App\Http\Requests\UpdateJabatanRequest;
use App\Http\Resources\Api\JabatanResource;
use App\Models\MJabatan;
use Illuminate\Http\Response;

class JabatanController extends Controller
{
    public function index()
    {
        $page = request('per_page');
        $columnKeyFilter = request('column_key');
        $columnValFilter = request('column_val');
        $sortBy = request('sort_by');
        $sortType = request('sort_type');

        $jabatans = MJabatan::select('id', 'nama', 'id_parent', 'level', 'created_at');

        $jabatans->when(!is_null($columnKeyFilter) && !is_null($columnValFilter), function ($query) use ($columnKeyFilter, $columnValFilter) {
            $query->where('nama', 'LIKE', "%{$columnValFilter[0]}%");
        });

        $jabatans->when(!is_null($sortBy) && !is_null($sortType), function ($query) use ($sortBy, $sortType) {
            $query->orderBy($sortBy, $sortType);
        }, function ($query) {
            $query->orderBy('id', 'desc');
        });

        return JabatanResource::collection($jabatans->latest()->paginate($page));
    }

    public function data()
    {
        $jabatans = MJabatan::with([
            'jabatan' => function ($query) {
                $query->select(
                    'id',
                    'nama',
                    'id_parent'
                );
            }
        ])->select(
            'id',
            'nama',
            'id_parent',
            'level'
        )->get();

        return response()->json(
            JabatanResource::collection($jabatans),
            Response::HTTP_OK
        );
    }

    public function store(StoreJabatanRequest $request)
    {
        $data = $request->validated();

        $level = 1; // inisiasi level

        $data = array_merge($data, [
            'level' => $level,
        ]);

        if (!is_null(request('id_parent'))) {

            $getLevel = MJabatan::find(request('id_parent'), ['id', 'id_parent', 'level']);

            $level = $getLevel->level_custom + 1;

            $data = array_merge($data, [
                'id_parent' => request('id_parent'),
                'level' => $level,
            ]);
        }

        MJabatan::create($data);

        return response()->json(
            'Tindakan Berhasil',
            Response::HTTP_CREATED
        );
    }

    public function show($id)
    {
        $jabatan = MJabatan::findOrFail($id);

        return response()->json(
            new JabatanResource($jabatan),
            Response::HTTP_OK
        );
    }

    public function update(UpdateJabatanRequest $request, $id)
    {
        $data = $request->validated();

        $level = 1;

        $data = array_merge($data, [
            'level' => $level,
        ]);

        if ($request->has('id_parent') && !is_null($request->id_parent)) {
            $getLevel = MJabatan::find($request->id_parent, ['id', 'id_parent', 'level']);

            $level = $getLevel->level + 1;

            $data = array_merge($data, [
                'level' => $level,
                'id_parent' => $request->id_parent,
            ]);
        }

        $jabatan = MJabatan::findOrFail($id);

        $jabatan->update($data);

        return response()->json(
            'Tindakan Berhasil',
            Response::HTTP_OK
        );
    }

    public function destroy($id)
    {
        $jabatan = MJabatan::findOrFail($id);
        $jabatan->delete();

        return response()->json('', Response::HTTP_NO_CONTENT);
    }
}
