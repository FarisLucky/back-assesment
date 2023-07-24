<?php

use App\Http\Controllers\HistoryPenilaianController;
use App\Http\Controllers\JabatanController;
use App\Models\MJabatan;
use App\Models\PersonalAccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pdf-view/{idPenilaian}', [HistoryPenilaianController::class, 'printUmum'])
    ->name('print.nilai');
Route::get('/pdf-khusus/{idPenilaian}', [HistoryPenilaianController::class, 'printKhusus'])
    ->name('print.nilai_khusus');
Route::get('/excel-view/{idPenilaian}', [HistoryPenilaianController::class, 'excelUmum'])
    ->name('excel.nilai');
Route::get('/jabatan/cetak', [JabatanController::class, 'pdf'])
    ->name('excel.nilai');

Route::get('/tokens/create', function (Request $request) {
    $user = PersonalAccessToken::find(1);
    dd($user);

    return ['token' => $token->plainTextToken];
});

Route::get('/test', function () {
    return Http::withHeaders([
        'Authorization' => 'Bearer 478b5d7b91373318f9c8c175016ba3c49220e60c9d4fb92dc540051eba1302fe',
        'Accept' => 'application/json'
    ])->get('http://localhost/simpeg/api/user');
});

Route::get('test-jabatan/', function () {
    return MJabatan::find(1);
});
