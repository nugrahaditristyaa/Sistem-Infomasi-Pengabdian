<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pengabdian;
use Illuminate\Support\Facades\DB;

echo "=== TEST DATA LUARAN DIRENCANAKAN UNTUK EDIT ===\n\n";

// Ambil beberapa pengabdian yang memiliki data luaran direncanakan
$pengabdianWithLuaran = Pengabdian::whereNotNull('jumlah_luaran_direncanakan')
    ->where('jumlah_luaran_direncanakan', '!=', '')
    ->limit(5)
    ->get(['id_pengabdian', 'judul_pengabdian', 'jumlah_luaran_direncanakan']);

echo "Pengabdian dengan data luaran direncanakan:\n\n";

foreach ($pengabdianWithLuaran as $pengabdian) {
    echo "ID: {$pengabdian->id_pengabdian}\n";
    echo "Judul: {$pengabdian->judul_pengabdian}\n";
    echo "Data Raw: ";
    var_dump($pengabdian->jumlah_luaran_direncanakan);

    // Simulasi processing seperti di view
    if (is_string($pengabdian->jumlah_luaran_direncanakan)) {
        $luaranArray = json_decode($pengabdian->jumlah_luaran_direncanakan, true);
        echo "Decoded: ";
        var_dump($luaranArray);
    } else {
        $luaranArray = $pengabdian->jumlah_luaran_direncanakan;
        echo "Direct Array: ";
        var_dump($luaranArray);
    }

    $selectedDirencanakan = is_array($luaranArray) ? $luaranArray : [];

    echo "Selected untuk checkbox: ";
    print_r($selectedDirencanakan);

    // Cek jenis luaran yang akan ditampilkan
    $jenisLuaran = DB::table('jenis_luaran')->get(['id_jenis_luaran', 'nama_jenis_luaran']);

    echo "Checkbox yang akan di-check:\n";
    foreach ($jenisLuaran as $jl) {
        $isChecked = in_array($jl->nama_jenis_luaran, $selectedDirencanakan);
        echo "- {$jl->nama_jenis_luaran}: " . ($isChecked ? "✅ CHECKED" : "⬜ UNCHECKED") . "\n";
    }

    echo "\n" . str_repeat("-", 50) . "\n\n";
}

if ($pengabdianWithLuaran->isEmpty()) {
    echo "❌ Tidak ada pengabdian dengan data luaran direncanakan\n";
    echo "Mari periksa semua pengabdian:\n\n";

    $allPengabdian = Pengabdian::limit(5)->get(['id_pengabdian', 'judul_pengabdian', 'jumlah_luaran_direncanakan']);

    foreach ($allPengabdian as $p) {
        echo "ID: {$p->id_pengabdian} | Luaran: ";
        if (is_null($p->jumlah_luaran_direncanakan)) {
            echo "NULL";
        } elseif (empty($p->jumlah_luaran_direncanakan)) {
            echo "EMPTY";
        } else {
            var_dump($p->jumlah_luaran_direncanakan);
        }
        echo "\n";
    }
}
