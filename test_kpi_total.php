<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TEST KPI IKT.I.5.i - Total HKI Semua Prodi ===\n\n";

// Simulasi filter tahun
$filterYears = ['2024', 'all'];

foreach ($filterYears as $filterYear) {
    echo "=== FILTER TAHUN: {$filterYear} ===\n";

    // Query yang sama seperti di dashboard
    $baseHkiQuery = DB::table('luaran')
        ->join('pengabdian', 'luaran.id_pengabdian', '=', 'pengabdian.id_pengabdian')
        ->join('jenis_luaran', 'luaran.id_jenis_luaran', '=', 'jenis_luaran.id_jenis_luaran')
        ->join('pengabdian_dosen', 'pengabdian.id_pengabdian', '=', 'pengabdian_dosen.id_pengabdian')
        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
        ->where('jenis_luaran.nama_jenis_luaran', 'HKI');

    // Filter tahun jika bukan 'all'
    if ($filterYear !== 'all') {
        $baseHkiQuery->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
    }

    // HKI Informatika
    $hkiInformatika = (clone $baseHkiQuery)
        ->where('dosen.prodi', 'Informatika')
        ->distinct('luaran.id_luaran')
        ->count('luaran.id_luaran');

    // HKI Sistem Informasi  
    $hkiSistemInformasi = (clone $baseHkiQuery)
        ->where('dosen.prodi', 'Sistem Informasi')
        ->distinct('luaran.id_luaran')
        ->count('luaran.id_luaran');

    $totalHki = $hkiInformatika + $hkiSistemInformasi;
    $informatikaTercapai = $hkiInformatika >= 1;
    $sistemInformasiTercapai = $hkiSistemInformasi >= 1;
    $keduaProdiTercapai = $informatikaTercapai && $sistemInformasiTercapai;

    echo "HKI Informatika: {$hkiInformatika} " . ($informatikaTercapai ? "✅" : "❌") . "\n";
    echo "HKI Sistem Informasi: {$hkiSistemInformasi} " . ($sistemInformasiTercapai ? "✅" : "❌") . "\n";
    echo "TOTAL HKI SEMUA PRODI: {$totalHki}\n";
    echo "Status Keseluruhan: " . ($keduaProdiTercapai ? "✅ TERCAPAI" : "❌ BELUM TERCAPAI") . "\n";

    // Return value untuk controller
    echo "Return Value untuk Controller: {$totalHki}\n";
    echo "---\n\n";
}

// Test return value dari controller (simulasi)
echo "=== SIMULASI CONTROLLER RETURN ===\n";
echo "Kode KPI: IKT.I.5.i\n";
echo "Method: calculateHkiPerProdiCount()\n";
echo "Return: \$hkiData['total'] (Total HKI semua prodi)\n";
echo "Untuk dashboard: Tampilkan breakdown per prodi + total\n";
echo "Untuk KPI calculation: Return total untuk perhitungan\n";
