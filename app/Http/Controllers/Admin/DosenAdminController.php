<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Dekan\DekanController;

class DosenAdminController extends Controller
{
    /**
     * Show rekap page for admin by delegating to DekanController.
     */
    public function rekap(Request $request)
    {
        return app(DekanController::class)->dosenRekap($request);
    }

    /**
     * Export rekap CSV for admin by delegating to DekanController.
     */
    public function exportRekap(Request $request)
    {
        return app(DekanController::class)->exportDosenRekap($request);
    }
}
