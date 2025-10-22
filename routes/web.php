<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PengabdianController;
use App\Http\Controllers\Admin\HkiController;
use App\Http\Controllers\Admin\DosenController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\KpiController;
use App\Http\Controllers\Admin\DokumenController;
use App\Http\Controllers\Admin\LuaranController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ==========================================================
//            PERBAIKAN UTAMA ADA DI BLOK INI
// ==========================================================
Route::middleware(['auth:admin'])->group(function () {

    // 1. TAMBAHKAN ROUTE REDIRECT INI
    // Mengarahkan /admin ke /admin/dashboard
    Route::get('/admin', function () {
        return redirect()->route('admin.dashboard');
    });

    // 2. GABUNGKAN SEMUA ROUTE ADMIN DALAM SATU GRUP
    Route::prefix('admin')->as('admin.')->group(function () {

        // 3. PINDAHKAN ROUTE DASHBOARD KE SINI
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Resource Routes (tetap sama)
        Route::resource('pengabdian', PengabdianController::class);
        Route::resource('dosen', DosenController::class);
        Route::resource('mahasiswa', MahasiswaController::class);
        Route::resource('dokumen', DokumenController::class);
        Route::resource('luaran', LuaranController::class);

        // HKI Routes (tetap sama)
        Route::get('hki', [HkiController::class, 'index'])->name('hki.index');
        Route::get('hki/{id}', [HkiController::class, 'show'])->name('hki.show');
    });
});
// ==========================================================
//                   AKHIR PERBAIKAN
// ==========================================================

// Shared API Routes (accessible by InQA, Kaprodi TI, Kaprodi SI)
Route::middleware(['auth:admin'])->group(function () {
    Route::prefix('inqa/api')->name('inqa.api.')->group(function () {
        Route::get('/sparkline-data', [App\Http\Controllers\InQA\InQaController::class, 'getSparklineData'])->name('sparkline-data');
        Route::get('/funding-sources', [App\Http\Controllers\InQA\InQaController::class, 'getFundingSourcesData'])->name('funding-sources');
        Route::get('/statistics-detail', [App\Http\Controllers\InQA\InQaController::class, 'getStatisticsDetail'])->name('statistics-detail');
    });
});

// InQA Routes
Route::middleware(['auth:admin', 'role:Staff InQA'])->group(function () {
    Route::prefix('inqa')->name('inqa.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\InQA\InQaController::class, 'dashboard'])->name('dashboard');
        Route::get('/pengabdian', [App\Http\Controllers\InQA\InQaController::class, 'pengabdian'])->name('pengabdian');
        Route::get('/dosen', [App\Http\Controllers\InQA\InQaController::class, 'dosen'])->name('dosen');
        Route::get('/mahasiswa', [App\Http\Controllers\InQA\InQaController::class, 'mahasiswa'])->name('mahasiswa');
        Route::get('/monitoring-kpi', [App\Http\Controllers\InQA\InQaController::class, 'monitoringKpi'])->name('monitoring-kpi');
        Route::get('/monitoring-kpi/{id}/edit', [App\Http\Controllers\InQA\InQaController::class, 'editMonitoringKpi'])->name('monitoring-kpi.edit');
        Route::put('/monitoring-kpi/{id}', [App\Http\Controllers\InQA\InQaController::class, 'updateMonitoringKpi'])->name('monitoring-kpi.update');
    });
});

// Kaprodi TI Routes
Route::middleware(['auth:admin', 'role:Kaprodi TI'])->group(function () {
    Route::prefix('kaprodi-ti')->name('kaprodi.ti.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Kaprodi\KaprodiController::class, 'dashboardTI'])->name('dashboard');
        Route::get('/pengabdian', [App\Http\Controllers\Kaprodi\KaprodiController::class, 'pengabdianList'])->name('pengabdian');

        // API Routes (menggunakan namespace inqa.api untuk compatibility dengan view)
    });
});

// Kaprodi SI Routes
Route::middleware(['auth:admin', 'role:Kaprodi SI'])->group(function () {
    Route::prefix('kaprodi-si')->name('kaprodi.si.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Kaprodi\KaprodiController::class, 'dashboardSI'])->name('dashboard');
        Route::get('/pengabdian', [App\Http\Controllers\Kaprodi\KaprodiController::class, 'pengabdianList'])->name('pengabdian');

        // API Routes (menggunakan namespace inqa.api untuk compatibility dengan view)
    });
});
