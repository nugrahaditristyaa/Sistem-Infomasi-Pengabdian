<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pengabdian;
use Illuminate\Support\Facades\DB;

echo "=== TESTING YEAR FILTER FOR KAPRODI ===\n\n";

// Simulate Kaprodi TI filter
$prodiFilter = 'Informatika';

$baseProdiFilter = function ($query) use ($prodiFilter) {
    $query->whereExists(function ($subQuery) use ($prodiFilter) {
        $subQuery->select(DB::raw(1))
            ->from('pengabdian_dosen')
            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
            ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
            ->where('dosen.prodi', $prodiFilter);
    });
};

echo "1. Data per Tahun untuk Informatika:\n";
$yearsData = Pengabdian::where($baseProdiFilter)
    ->selectRaw('YEAR(tanggal_pengabdian) as year, COUNT(*) as count')
    ->groupBy('year')
    ->orderBy('year', 'desc')
    ->get();

foreach ($yearsData as $data) {
    echo "   - {$data->year}: {$data->count} pengabdian\n";
}

echo "\n2. Available Years untuk dropdown:\n";
$availableYears = Pengabdian::where($baseProdiFilter)
    ->selectRaw('YEAR(tanggal_pengabdian) as year')
    ->distinct()
    ->orderBy('year', 'desc')
    ->pluck('year')
    ->toArray();

echo "   " . implode(', ', $availableYears) . "\n";

echo "\n3. Test Filter untuk Tahun 2020 (Informatika):\n";
$filterYear = 2020;
$totalPengabdian = Pengabdian::where($baseProdiFilter)
    ->whereYear('tanggal_pengabdian', $filterYear)
    ->count();
echo "   Total: {$totalPengabdian} pengabdian\n";

echo "\n4. Test Filter untuk Tahun 2019 (Informatika):\n";
$filterYear = 2019;
$totalPengabdian = Pengabdian::where($baseProdiFilter)
    ->whereYear('tanggal_pengabdian', $filterYear)
    ->count();
echo "   Total: {$totalPengabdian} pengabdian\n";

echo "\n5. Test Filter 'all' (Informatika):\n";
$totalPengabdian = Pengabdian::where($baseProdiFilter)->count();
echo "   Total: {$totalPengabdian} pengabdian\n";

// Test untuk Sistem Informasi
echo "\n\n=== SISTEM INFORMASI ===\n\n";
$prodiFilter = 'Sistem Informasi';

$baseProdiFilter = function ($query) use ($prodiFilter) {
    $query->whereExists(function ($subQuery) use ($prodiFilter) {
        $subQuery->select(DB::raw(1))
            ->from('pengabdian_dosen')
            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
            ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
            ->where('dosen.prodi', $prodiFilter);
    });
};

echo "1. Data per Tahun untuk Sistem Informasi:\n";
$yearsData = Pengabdian::where($baseProdiFilter)
    ->selectRaw('YEAR(tanggal_pengabdian) as year, COUNT(*) as count')
    ->groupBy('year')
    ->orderBy('year', 'desc')
    ->get();

foreach ($yearsData as $data) {
    echo "   - {$data->year}: {$data->count} pengabdian\n";
}

echo "\n2. Test Filter 'all' (Sistem Informasi):\n";
$totalPengabdian = Pengabdian::where($baseProdiFilter)->count();
echo "   Total: {$totalPengabdian} pengabdian\n";

echo "\n=== END TESTING ===\n";
