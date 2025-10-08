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

// Staff InQA: KPI Management
Route::middleware(['auth:admin', 'role:Staff InQA'])->group(function () {
    Route::prefix('inqa')->as('inqa.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\InQA\InQaController::class, 'dashboard'])->name('dashboard');
        Route::resource('kpi', \App\Http\Controllers\Inqa\InqaKpiController::class);

        // buat route khusus untuk update KPI berdasarkan kode (untuk modal AJAX)
        Route::put('kpi/update/{kode}', [\App\Http\Controllers\Inqa\InqaKpiController::class, 'updateByCode'])->name('kpi.updateByCode');
    });
});
