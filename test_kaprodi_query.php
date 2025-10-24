<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pengabdian;
use App\Models\Dosen;
use Illuminate\Support\Facades\DB;

echo "=== DEBUGGING KAPRODI QUERY ===\n\n";

// Test 1: Basic counts
echo "1. Basic Counts:\n";
echo "   Total Pengabdian: " . Pengabdian::count() . "\n";
echo "   Total Dosen: " . Dosen::count() . "\n";
echo "   Dosen Informatika: " . Dosen::where('prodi', 'Informatika')->count() . "\n";
echo "   Dosen Sistem Informasi: " . Dosen::where('prodi', 'Sistem Informasi')->count() . "\n";
echo "   Total pengabdian_dosen records: " . DB::table('pengabdian_dosen')->count() . "\n\n";

// Test 2: Sample dosen prodi values
echo "2. Sample Dosen Prodi Values (first 10):\n";
$sampleDosen = DB::table('pengabdian_dosen')
    ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
    ->select('dosen.nama', 'dosen.prodi')
    ->limit(10)
    ->get();
foreach ($sampleDosen as $d) {
    echo "   - {$d->nama}: '{$d->prodi}'\n";
}
echo "\n";

// Test 3: Count distinct prodi values in dosen table
echo "3. Distinct Prodi Values in Dosen Table:\n";
$prodiValues = DB::table('dosen')->select('prodi')->distinct()->get();
foreach ($prodiValues as $p) {
    $count = Dosen::where('prodi', $p->prodi)->count();
    echo "   - '{$p->prodi}': {$count} dosen\n";
}
echo "\n";

// Test 4: Test the whereExists query for Informatika
echo "4. Testing whereExists Query for Informatika:\n";
$countTI = Pengabdian::whereExists(function ($q) {
    $q->select(DB::raw(1))
        ->from('pengabdian_dosen')
        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
        ->where('dosen.prodi', 'Informatika');
})->count();
echo "   Result: {$countTI} pengabdian\n\n";

// Test 5: Test the whereExists query for Sistem Informasi
echo "5. Testing whereExists Query for Sistem Informasi:\n";
$countSI = Pengabdian::whereExists(function ($q) {
    $q->select(DB::raw(1))
        ->from('pengabdian_dosen')
        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
        ->where('dosen.prodi', 'Sistem Informasi');
})->count();
echo "   Result: {$countSI} pengabdian\n\n";

// Test 6: Try with different prodi name variations
echo "6. Testing with Prodi Name Variations:\n";
$variations = ['informatika', 'INFORMATIKA', 'Teknik Informatika', 'sistem informasi', 'SISTEM INFORMASI'];
foreach ($variations as $var) {
    $count = Pengabdian::whereExists(function ($q) use ($var) {
        $q->select(DB::raw(1))
            ->from('pengabdian_dosen')
            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
            ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
            ->where('dosen.prodi', $var);
    })->count();
    echo "   - '{$var}': {$count} pengabdian\n";
}
echo "\n";

// Test 7: Get actual SQL query
echo "7. SQL Query Being Generated:\n";
$query = Pengabdian::whereExists(function ($q) {
    $q->select(DB::raw(1))
        ->from('pengabdian_dosen')
        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
        ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
        ->where('dosen.prodi', 'Informatika');
});
echo "   " . $query->toSql() . "\n";
echo "   Bindings: " . json_encode($query->getBindings()) . "\n\n";

// Test 8: Alternative approach - direct join count
echo "8. Alternative Approach (Direct Join Count):\n";
$countTIDirect = DB::table('pengabdian')
    ->join('pengabdian_dosen', 'pengabdian.id_pengabdian', '=', 'pengabdian_dosen.id_pengabdian')
    ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
    ->where('dosen.prodi', 'Informatika')
    ->distinct('pengabdian.id_pengabdian')
    ->count('pengabdian.id_pengabdian');
echo "   Informatika (direct join): {$countTIDirect} pengabdian\n";

$countSIDirect = DB::table('pengabdian')
    ->join('pengabdian_dosen', 'pengabdian.id_pengabdian', '=', 'pengabdian_dosen.id_pengabdian')
    ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
    ->where('dosen.prodi', 'Sistem Informasi')
    ->distinct('pengabdian.id_pengabdian')
    ->count('pengabdian.id_pengabdian');
echo "   Sistem Informasi (direct join): {$countSIDirect} pengabdian\n\n";

echo "=== END DEBUGGING ===\n";
