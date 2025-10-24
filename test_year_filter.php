<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pengabdian;
use Illuminate\Support\Facades\DB;

echo "=== DEBUGGING YEAR FILTER ===\n\n";

$currentYear = date('Y'); // 2025
echo "Current Year: {$currentYear}\n\n";

// Test 1: Check available years in pengabdian
echo "1. Available Years in Pengabdian:\n";
$years = Pengabdian::selectRaw('YEAR(tanggal_pengabdian) as year, COUNT(*) as count')
    ->groupBy('year')
    ->orderBy('year', 'desc')
    ->get();
foreach ($years as $y) {
    echo "   - {$y->year}: {$y->count} pengabdian\n";
}
echo "\n";

// Test 2: Filter for year 2025 (current year)
echo "2. Pengabdian in Year 2025:\n";
$count2025 = Pengabdian::whereYear('tanggal_pengabdian', 2025)->count();
echo "   Total: {$count2025}\n\n";

// Test 3: Pengabdian in 2025 with Informatika filter
echo "3. Pengabdian in 2025 for Informatika:\n";
$count2025TI = Pengabdian::whereYear('tanggal_pengabdian', 2025)
    ->whereExists(function ($q) {
        $q->select(DB::raw(1))
            ->from('pengabdian_dosen')
            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
            ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
            ->where('dosen.prodi', 'Informatika');
    })->count();
echo "   Total: {$count2025TI}\n\n";

// Test 4: Sample pengabdian dates
echo "4. Sample Pengabdian Dates (last 10):\n";
$samples = Pengabdian::orderBy('tanggal_pengabdian', 'desc')
    ->limit(10)
    ->get(['id_pengabdian', 'judul_pengabdian', 'tanggal_pengabdian']);
foreach ($samples as $s) {
    $year = date('Y', strtotime($s->tanggal_pengabdian));
    echo "   - {$s->id_pengabdian}: {$s->tanggal_pengabdian} (Year: {$year})\n";
}
echo "\n";

// Test 5: Testing the dashboard logic with year filter
echo "5. Dashboard Query Test (Year 2025, Informatika):\n";
$filterYear = 2025;
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

$totalPengabdian = Pengabdian::where($baseProdiFilter)
    ->whereYear('tanggal_pengabdian', $filterYear)
    ->count();

echo "   Total Pengabdian (using dashboard logic): {$totalPengabdian}\n\n";

// Test 6: Test without year filter
echo "6. Dashboard Query Test (All Years, Informatika):\n";
$totalPengabdianAll = Pengabdian::where($baseProdiFilter)->count();
echo "   Total Pengabdian (all years): {$totalPengabdianAll}\n\n";

echo "=== END DEBUGGING ===\n";
