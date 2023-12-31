<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {

        try {

            $request->validate([
                'email' => 'required',
                'password' => 'required',
                'device_name' => 'required'
            ]);

            $user = User::with([
                'karyawan' => function ($query) {
                    $query->select('id', 'nama', 'id_unit', 'id_jabatan');
                },
                'karyawan.jabatan' => function ($query) {
                    $query->select('id', 'nama', 'level', 'kategori');
                }
            ])
                ->select('id', 'id_karyawan', 'name', 'email', 'password', 'role')
                ->where('email', $request->email)
                ->firstOrFail();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['akun tidak ditemukan']
                ]);
            }

            return response()->json([
                'user' => $user,
                'token' => $user->createToken($request->device_name)->plainTextToken
            ]);
        } catch (\Throwable $th) {

            return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $user->currentAccessToken()->delete();

            return response()->json('', Response::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
