<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJabatanRequest;
use App\Http\Requests\UpdateJabatanRequest;
use App\Http\Resources\Api\JabatanResource;
use App\Models\MJabatan;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class JabatanController extends Controller
{
    public function index()
    {
        // Gate::denyIf('user', 'MAAF ANDA TIDAK MEMILIKI AKSES KE HALAMAN INI', Response::HTTP_FORBIDDEN);

        $page = request('per_page');
        $columnKeyFilter = request('column_key');
        $columnValFilter = request('column_val');
        $sortBy = request('sort_by');
        $sortType = request('sort_type');

        $relations = Arr::get(config('relationship'), MJabatan::class);

        $jabatans = MJabatan::with('parent')
            ->select('id', 'nama', 'id_parent', 'level', 'created_at', 'updated_at');

        $jabatans->when(!is_null($columnKeyFilter) && !is_null($columnValFilter), function ($query) use ($columnKeyFilter, $columnValFilter, $relations) {
            for ($i = 0; $i < count($columnKeyFilter); $i++) {
                if (str_contains($columnKeyFilter[$i], '.')) { // check ada titik ndak

                    $relationQuery = explode('.', $columnKeyFilter[$i]);

                    $getRelation = Arr::where($relations, function ($value) use ($relationQuery) {
                        return $value == $relationQuery[1];
                    });

                    $relation = array_keys($getRelation)[0];

                    $query->whereHas($relation, function ($query) use ($columnValFilter, $relationQuery, $i) {
                        $query->where($relationQuery[2], 'LIKE', "%{$columnValFilter[$i]}%");
                    });
                } else {

                    $query->where($columnKeyFilter[$i], 'LIKE', "%{$columnValFilter[$i]}%");
                }
            }
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
            'parent' => function ($query) {
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
            'level',
            'created_at',
            'updated_at'
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

    public function pdf()
    {
        $jabatans = MJabatan::all();

        return view('jabatan.pdf', compact('jabatans'));
    }

    public function destroy($id)
    {
        $jabatan = MJabatan::findOrFail($id);
        $jabatan->delete();

        return response()->json('', Response::HTTP_NO_CONTENT);
    }
}
