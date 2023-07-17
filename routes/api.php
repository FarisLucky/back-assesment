<?php

use App\Http\Controllers\AnalisisSwotController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DetailPenilaianKaryawanController;
use App\Http\Controllers\HistoryPenilaianController;
use App\Http\Controllers\MKaryawanController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\MPenilaianController;
use App\Http\Controllers\MSubPenilaianController;
use App\Http\Controllers\MTipeController;
use App\Http\Controllers\MTipePenilaianController;
use App\Http\Controllers\MValidasiPenilaiController;
use App\Http\Controllers\PenilaianKaryawanController;
use App\Http\Controllers\SubPenilaianKaryawanController;
use App\Http\Controllers\UnitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('karyawans/all/data', [MKaryawanController::class, 'data'])
        ->name('karyawans.data');
    Route::apiResource('karyawans', MKaryawanController::class)
        ->names('mKaryawans');

    Route::get('jabatans/all/data', [JabatanController::class, 'data'])
        ->name('karyawans.data');
    Route::apiResource('jabatans', JabatanController::class)
        ->names('mJabatans');

    Route::get('units/all/data', [UnitController::class, 'data'])
        ->name('units.data');
    Route::apiResource('units', UnitController::class)
        ->names('mUnits');

    Route::get('tipes/all/data', [MTipeController::class, 'data'])
        ->name('mTipes.data');
    Route::get('tipes/by/{tipe}', [MTipeController::class, 'tampilByTipe'])
        ->name('mTipes.data');
    Route::apiResource('tipes', MTipeController::class)
        ->names('mTipes');

    Route::apiResource('tipe-penilaians', MTipePenilaianController::class)
        ->except('update')
        ->names('mTipePenilaians');
    Route::put('tipe-penilaians/update', [MTipePenilaianController::class, 'update'])
        ->name('mTipePenilaians.update');

    Route::get('penilaians/all/data', [MPenilaianController::class, 'data'])
        ->name('mPenilaians.data');
    Route::apiResource('penilaians', MPenilaianController::class)
        ->names('mPenilaians');

    Route::get('sub-penilaians/all/data', [MSubPenilaianController::class, 'data'])
        ->name('mSub-penilaians.data');
    Route::get('sub-penilaians/khusus/data', [MSubPenilaianController::class, 'dataKhusus'])
        ->name('mSub-penilaians.dataKhusus');
    Route::apiResource('sub-penilaians', MSubPenilaianController::class)
        ->names('mSub-penilaians');

    Route::get('penilaian-karyawans/get-nilai/{idKaryawan}/{tipe}', [PenilaianKaryawanController::class, 'getNilai'])
        ->name('penilaian-karyawan.data');
    Route::get('penilaian-karyawans/all/data', [PenilaianKaryawanController::class, 'data'])
        ->name('penilaian-karyawan.data');
    Route::get('penilaian-karyawans/{id_penilaian}/progress', [PenilaianKaryawanController::class, 'showProgress'])
        ->name('penilaian-karyawan.showProgress');
    Route::get('penilaian-karyawans/get-nilai/{idKaryawan}/{tipe}', [PenilaianKaryawanController::class, 'getNilai'])
        ->name('penilaian-karyawan.data');
    Route::get('penilaian-karyawan-progress', [PenilaianKaryawanController::class, 'indexProgress'])
        ->name('penilaian-karyawan.progress');
    Route::apiResource('penilaian-karyawans', PenilaianKaryawanController::class)
        ->except('show')
        ->names('penilaian-karyawan');

    Route::get('sub-penilaian-karyawans/all/data', [SubPenilaianKaryawanController::class, 'data'])
        ->name('sub-penilaian-karyawan.data');
    Route::apiResource('sub-penilaian-karyawans', SubPenilaianKaryawanController::class)
        ->names('sub-penilaian-karyawan');

    Route::get('detail-penilaian-karyawans/all/data', [DetailPenilaianKaryawanController::class, 'data'])
        ->name('detail-penilaian-karyawan.data');
    Route::apiResource('detail-penilaian-karyawans', DetailPenilaianKaryawanController::class)
        ->names('detail-penilaian-karyawan');

    Route::get('analisis-swot-penilaian/all/data', [AnalisisSwotController::class, 'data'])
        ->name('analisis-swot-penilaian.data');
    Route::apiResource('analisis-swot-penilaian', AnalisisSwotController::class)
        ->names('analisis-swot-penilaian');

    Route::get('history-penilaian', [HistoryPenilaianController::class, 'data'])
        ->name('history-penilaian.data');
    // Route::get('history-penilaian/{id}/{tipe}/{month}/{year}', [HistoryPenilaianController::class, 'show'])
    //     ->name('history-penilaian.show');
    Route::apiResource('history-penilaian', HistoryPenilaianController::class)
        ->except('show')
        ->names('history-penilaian');

    Route::get('history-penilaian/{id}/{tipe}/{month}/{year}', [HistoryPenilaianController::class, 'show'])
        ->name('history-penilaian.show');

    Route::get('tipes/by/{tipe}', [MTipeController::class, 'tampilByTipe'])
        ->name('mTipes.data');

    Route::get('tipes/all/data', [MTipeController::class, 'data'])
        ->name('mTipes.data');

    Route::apiResource('m-validasi-penilai', MValidasiPenilaiController::class)
        ->names('m-validasi-penilai');
});
/**
 * Authentication route
 *
 **/
Route::post('login', [LoginController::class, 'login'])->name('auth.login');

// Route::get('penilaian-karyawans/{id_penilaian}/progress', [PenilaianKaryawanController::class, 'showProgress'])
    // ->name('penilaian-karyawan.showProgress');
