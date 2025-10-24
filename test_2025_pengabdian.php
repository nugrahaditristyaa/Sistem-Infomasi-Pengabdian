<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== 2025 PENGABDIAN DETAILS ===\n\n";

$pengabdian2025 = DB::table('pengabdian')
    ->whereYear('tanggal_pengabdian', 2025)
    ->get(['id_pengabdian', 'judul_pengabdian']);

foreach ($pengabdian2025 as $p) {
    echo "Pengabdian ID: {$p->id_pengabdian}\n";
    echo "Judul: {$p->judul_pengabdian}\n";
    echo "Dosen yang terlibat:\n";

    $dosenList = DB::table('pengabdian_dosen')
        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
        ->where('pengabdian_dosen.id_pengabdian', $p->id_pengabdian)
        ->get(['dosen.nama', 'dosen.prodi']);

    if ($dosenList->isEmpty()) {
        echo "  - TIDAK ADA DOSEN YANG TERDAFTAR!\n";
    } else {
        foreach ($dosenList as $d) {
            echo "  - {$d->nama} (Prodi: {$d->prodi})\n";
        }
    }
    echo "\n";
}

echo "=== END ===\n";
