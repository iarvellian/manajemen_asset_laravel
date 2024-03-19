<?php

use App\Http\Controllers\assetController;
use App\Http\Controllers\authController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\userController;
use App\Http\Controllers\refDivisiController;
use App\Http\Controllers\refKelasAsetController;
use App\Http\Controllers\refKodeProjekController;
use App\Http\Controllers\refLokasiController;
use App\Http\Controllers\refRoleController;
use App\Http\Controllers\transaksiAssetKeluarController;
use App\Http\Controllers\transaksiAssetMasukController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::post('/login', [authController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Route registered user
    Route::post('/register', [authController::class, 'register']);

    // Route logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Route profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [AuthController::class, 'profile']);
        Route::post('/', [AuthController::class, 'updateProfile']);
    });

    // Route log aktivitas
    Route::get('/log_aktivitas', [userController::class, 'logActivity']);

    // Route users
    Route::prefix('users')->group(function (){
        Route::get('/', [userController::class, 'getAllUsers']);
        Route::delete('/{id}', [userController::class, 'deleteUser']);
    });

    // Route role
    Route::prefix('role')->group(function (){
        Route::get('/', [refRoleController::class, 'index']);
        Route::post('/', [refRoleController::class, 'store']);
        Route::get('/{id_role}', [refRoleController::class, 'show']);
        Route::put('/{id_role}', [refRoleController::class, 'update']);
        Route::delete('/{id_role}', [refRoleController::class, 'destroy']);
    });

    // Route divisi
    Route::prefix('divisi')->group(function (){
        Route::get('/', [refDivisiController::class, 'index']);
        Route::post('/', [refDivisiController::class, 'store']);
        Route::get('/{id_divisi}', [refDivisiController::class, 'show']);
        Route::put('/{id_divisi}', [refDivisiController::class, 'update']);
        Route::delete('/{id_divisi}', [refDivisiController::class, 'destroy']);
    });

    // Route kelas aset
    Route::prefix('kelas_aset')->group(function (){
        Route::get('/', [refKelasAsetController::class, 'index']);
        Route::post('', [refKelasAsetController::class, 'store']);
        Route::get('/{id_kelas_aset}', [refKelasAsetController::class, 'show']);
        Route::put('/{id_kelas_aset}', [refKelasAsetController::class, 'update']);
        Route::delete('/{id_kelas_aset}', [refKelasAsetController::class, 'destroy']);
    });

    // Route kode projek
    Route::prefix('kode_projek')->group(function (){
        Route::get('/', [refKodeProjekController::class, 'index']);
        Route::post('/', [refKodeProjekController::class, 'store']);
        Route::get('/{id_kode_projek}', [refKodeProjekController::class, 'show']);
        Route::put('/{id_kode_projek}', [refKodeProjekController::class, 'update']);
        Route::delete('/{id_kode_projek}', [refKodeProjekController::class, 'destroy']);
    });

    // Route lokasi
    Route::prefix('lokasi')->group(function (){
        Route::get('/', [refLokasiController::class, 'index']);
        Route::post('/', [refLokasiController::class, 'store']);
        Route::get('/{id_lokasi}', [refLokasiController::class, 'show']);
        Route::put('/{id_lokasi}', [refLokasiController::class, 'update']);
        Route::delete('/{id_lokasi}', [refLokasiController::class, 'destroy']);
    });

    // Route asset
    Route::prefix('asset')->group(function (){
        Route::get('/', [assetController::class, 'index']);
        Route::post('/', [assetController::class, 'store']);
        Route::get('/{id_asset}', [assetController::class, 'show']);
        Route::put('/{id_asset}', [assetController::class, 'update']);
        Route::delete('/{id_asset}', [assetController::class, 'destroy']);
    });

    // Route asset keluar
    Route::prefix('assetkeluar')->group(function (){
        Route::get('/', [transaksiAssetKeluarController::class, 'index']);
        Route::post('/', [transaksiAssetKeluarController::class, 'store']);
        Route::get('/{id_asset_keluar}', [transaksiAssetKeluarController::class, 'show']);
        Route::put('/{id_asset_keluar}', [transaksiAssetKeluarController::class, 'update']);
        Route::delete('/{id_asset_keluar}', [transaksiAssetKeluarController::class, 'destroy']);
    });

    // Route asset masuk
    Route::prefix('assetmasuk')->group(function (){
        Route::get('/', [transaksiAssetMasukController::class, 'index']);
        Route::post('/', [transaksiAssetMasukController::class, 'store']);
        Route::get('/{id_asset_masuk}', [transaksiAssetMasukController::class, 'show']);
        Route::put('/{id_asset_masuk}', [transaksiAssetMasukController::class, 'update']);
        Route::delete('/{id_asset_masuk}', [transaksiAssetMasukController::class, 'destroy']);
    });

    // Route dashboard
    Route::get('/count_role', [dashboardController::class, 'getRoleCount']);
    Route::get('/count_user', [dashboardController::class, 'getUserCount']);
    Route::get('/count_lokasi', [dashboardController::class, 'getLokasiCount']);
    Route::get('/count_kelas_aset', [dashboardController::class, 'getKelasAsetCount']);
    Route::get('/count_kode_projek', [dashboardController::class, 'getKodeProjekCount']);
    Route::get('/count_divisi', [dashboardController::class, 'getDivisiCount']);
    Route::get('/count_asset', [dashboardController::class, 'getAssetCount']);
});
