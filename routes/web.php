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

        // Export route for pengabdian (place before resource to avoid wildcard collisions)
        Route::get('pengabdian/export', [App\Http\Controllers\Admin\PengabdianController::class, 'export'])->name('pengabdian.export');
        // Template and Import routes for pengabdian
        Route::get('pengabdian/template', [App\Http\Controllers\Admin\PengabdianController::class, 'template'])->name('pengabdian.template');
        Route::post('pengabdian/import', [App\Http\Controllers\Admin\PengabdianController::class, 'import'])->name('pengabdian.import');

        // Resource Routes (tetap sama)
        Route::resource('pengabdian', PengabdianController::class);

        // Rekap Dosen (accessible to Admin) - place before resource('dosen') to avoid wildcard collision
        Route::get('dosen/rekap', [App\Http\Controllers\Admin\DosenRekapController::class, 'rekap'])->name('dosen.rekap');
        Route::get('dosen/rekap/export', [App\Http\Controllers\Admin\DosenRekapController::class, 'exportRekap'])->name('dosen.rekap.export');
        // Use explicit detail path to avoid wildcard collision with resource routes (e.g. 'create')
        Route::get('dosen/detail/{nik}', [App\Http\Controllers\Admin\DosenRekapController::class, 'dosenDetail'])->name('dosen.detail');

        Route::resource('dosen', DosenController::class);
        Route::resource('mahasiswa', MahasiswaController::class);
        Route::resource('dokumen', DokumenController::class);
        Route::resource('luaran', LuaranController::class);

        // HKI Routes (tetap sama)
        Route::get('hki', [HkiController::class, 'index'])->name('hki.index');
        Route::get('hki/{id}', [HkiController::class, 'show'])->name('hki.show');

        // Rekap Dosen (accessible to Admin)
        Route::get('dosen/rekap', [App\Http\Controllers\Admin\DosenRekapController::class, 'rekap'])->name('dosen.rekap');
        Route::get('dosen/rekap/export', [App\Http\Controllers\Admin\DosenRekapController::class, 'exportRekap'])->name('dosen.rekap.export');
        Route::get('dosen/detail/{nik}', [App\Http\Controllers\Admin\DosenRekapController::class, 'dosenDetail'])->name('dosen.detail');
    });
});
// ==========================================================
//                   AKHIR PERBAIKAN
// ==========================================================

// Shared API Routes (accessible by Dekan, Kaprodi TI, Kaprodi SI)
Route::middleware(['auth:admin'])->group(function () {
    Route::prefix('dekan/api')->name('dekan.api.')->group(function () {
        Route::get('/sparkline-data', [App\Http\Controllers\Dekan\DekanController::class, 'getSparklineData'])->name('sparkline-data');
        Route::get('/funding-sources', [App\Http\Controllers\Dekan\DekanController::class, 'getFundingSourcesData'])->name('funding-sources');
        Route::get('/statistics-detail', [App\Http\Controllers\Dekan\DekanController::class, 'getStatisticsDetail'])->name('statistics-detail');
    });
});

// Dekan Routes
Route::middleware(['auth:admin', 'role:Dekan'])->group(function () {
    Route::prefix('dekan')->name('dekan.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Dekan\DekanController::class, 'dashboard'])->name('dashboard');
        Route::get('/pengabdian', [App\Http\Controllers\Dekan\DekanController::class, 'pengabdian'])->name('pengabdian');
        Route::get('/dosen', [App\Http\Controllers\Dekan\DekanController::class, 'dosen'])->name('dosen');
        Route::get('/dosen/rekap', [App\Http\Controllers\Dekan\DekanController::class, 'dosenRekap'])->name('dosen.rekap');
        Route::get('/dosen/rekap/export', [App\Http\Controllers\Dekan\DekanController::class, 'exportDosenRekap'])->name('dosen.rekap.export');
        Route::get('/dosen/{nik}', [App\Http\Controllers\Dekan\DekanController::class, 'dosenDetail'])->name('dosen.detail');
        Route::get('/mahasiswa', [App\Http\Controllers\Dekan\DekanController::class, 'mahasiswa'])->name('mahasiswa');

        // KPI Routes
        Route::get('/kpi', [App\Http\Controllers\Dekan\DekanKpiController::class, 'index'])->name('kpi.index');
        Route::put('/kpi/update/{kode}', [App\Http\Controllers\Dekan\DekanKpiController::class, 'updateByCode'])->name('kpi.updateByCode');
    });
});

// Kaprodi TI Routes
Route::middleware(['auth:admin', 'role:Kaprodi TI'])->group(function () {
    Route::prefix('kaprodi-ti')->name('kaprodi.ti.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Kaprodi\KaprodiController::class, 'dashboardTI'])->name('dashboard');
        Route::get('/pengabdian', [App\Http\Controllers\Kaprodi\KaprodiController::class, 'pengabdianList'])->name('pengabdian');

        // Rekap Dosen
        Route::get('/dosen/rekap', [App\Http\Controllers\Kaprodi\KaprodiController::class, 'dosenRekapTI'])->name('dosen.rekap');
        Route::get('/dosen/rekap/export', [App\Http\Controllers\Kaprodi\KaprodiController::class, 'exportDosenRekapTI'])->name('dosen.rekap.export');
        Route::get('/dosen/{nik}', [App\Http\Controllers\Kaprodi\KaprodiController::class, 'dosenDetail'])->name('dosen.detail');

        // API Routes (menggunakan namespace dekan.api untuk compatibility dengan view)
    });
});

// Kaprodi SI Routes
Route::middleware(['auth:admin', 'role:Kaprodi SI'])->group(function () {
    Route::prefix('kaprodi-si')->name('kaprodi.si.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Kaprodi\KaprodiController::class, 'dashboardSI'])->name('dashboard');
        Route::get('/pengabdian', [App\Http\Controllers\Kaprodi\KaprodiController::class, 'pengabdianList'])->name('pengabdian');

        // Rekap Dosen
        Route::get('/dosen/rekap', [App\Http\Controllers\Kaprodi\KaprodiController::class, 'dosenRekapSI'])->name('dosen.rekap');
        Route::get('/dosen/rekap/export', [App\Http\Controllers\Kaprodi\KaprodiController::class, 'exportDosenRekapSI'])->name('dosen.rekap.export');
        Route::get('/dosen/{nik}', [App\Http\Controllers\Kaprodi\KaprodiController::class, 'dosenDetail'])->name('dosen.detail');

        // API Routes (menggunakan namespace dekan.api untuk compatibility dengan view)
    });
});
