<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Kpi;

echo "=== TESTING BERBAGAI URUTAN RADAR CHART ===\n\n";

// Sample data KPI untuk testing
$sampleKpiData = [
    ['kode' => 'IKT.I.5.g', 'realisasi' => 75.50, 'persentase' => 100.0, 'target' => 70],
    ['kode' => 'IKT.I.5.h', 'realisasi' => 45.25, 'persentase' => 64.6, 'target' => 70],
    ['kode' => 'IKT.I.5.i', 'realisasi' => 5.00, 'persentase' => 100.0, 'target' => 1],
    ['kode' => 'IKT.I.5.j', 'realisasi' => 85.75, 'persentase' => 100.0, 'target' => 80],
    ['kode' => 'PGB.I.1.1', 'realisasi' => 90.25, 'persentase' => 100.0, 'target' => 80],
    ['kode' => 'PGB.I.5.6', 'realisasi' => 8.75, 'persentase' => 87.5, 'target' => 10],
    ['kode' => 'PGB.I.7.4', 'realisasi' => 20.50, 'persentase' => 68.3, 'target' => 30],
    ['kode' => 'PGB.I.7.9', 'realisasi' => 15.25, 'persentase' => 100.0, 'target' => 10]
];

// Function untuk menghitung "smoothness score" dari urutan
function calculateSmoothness($data)
{
    $score = 0;
    $realisasiValues = array_column($data, 'realisasi');

    for ($i = 0; $i < count($realisasiValues) - 1; $i++) {
        $diff = abs($realisasiValues[$i] - $realisasiValues[$i + 1]);
        $score += $diff;
    }

    // Tambahan penalti untuk lompatan besar (>50 poin)
    for ($i = 0; $i < count($realisasiValues) - 1; $i++) {
        $diff = abs($realisasiValues[$i] - $realisasiValues[$i + 1]);
        if ($diff > 50) {
            $score += $diff * 0.5; // Penalti tambahan
        }
    }

    return $score;
}

// Function untuk visualisasi urutan
function visualizeOrder($data, $title)
{
    echo "=== $title ===\n";
    $labels = [];
    $realisasi = [];

    foreach ($data as $index => $kpi) {
        $labels[] = $kpi['kode'];
        $realisasi[] = $kpi['realisasi'];

        echo sprintf(
            "%d. %s: %.2f (%.1f%%) %s\n",
            $index + 1,
            $kpi['kode'],
            $kpi['realisasi'],
            $kpi['persentase'],
            $kpi['persentase'] >= 100 ? '✅' : '⚠️'
        );
    }

    $smoothness = calculateSmoothness($data);
    echo "📊 Smoothness Score: $smoothness (semakin kecil = semakin smooth)\n";
    echo "📋 Labels: ['" . implode("', '", $labels) . "']\n";
    echo "📈 Values: [" . implode(", ", $realisasi) . "]\n\n";

    return $smoothness;
}

echo "🧪 TESTING 6 STRATEGI URUTAN BERBEDA:\n\n";

// 1. Urutan berdasarkan realisasi (current implementation)
$strategy1 = $sampleKpiData;
usort($strategy1, function ($a, $b) {
    if ($a['realisasi'] == $b['realisasi']) {
        return $b['persentase'] <=> $a['persentase'];
    }
    return $b['realisasi'] <=> $a['realisasi'];
});

$score1 = visualizeOrder($strategy1, "STRATEGI 1: Realisasi Tertinggi → Terendah (Current)");

// 2. Urutan berdasarkan persentase capaian
$strategy2 = $sampleKpiData;
usort($strategy2, function ($a, $b) {
    if ($a['persentase'] == $b['persentase']) {
        return $b['realisasi'] <=> $a['realisasi'];
    }
    return $b['persentase'] <=> $a['persentase'];
});

$score2 = visualizeOrder($strategy2, "STRATEGI 2: Persentase Capaian Tertinggi → Terendah");

// 3. Urutan bergantian tinggi-rendah (alternating)
$strategy3 = $sampleKpiData;
usort($strategy3, function ($a, $b) {
    return $b['realisasi'] <=> $a['realisasi'];
});
$alternating = [];
$high = [];
$low = [];

for ($i = 0; $i < count($strategy3); $i++) {
    if ($i < count($strategy3) / 2) {
        $high[] = $strategy3[$i];
    } else {
        $low[] = $strategy3[$i];
    }
}

$low = array_reverse($low);
for ($i = 0; $i < max(count($high), count($low)); $i++) {
    if (isset($high[$i])) $alternating[] = $high[$i];
    if (isset($low[$i])) $alternating[] = $low[$i];
}

$score3 = visualizeOrder($alternating, "STRATEGI 3: Alternating High-Low");

// 4. Urutan berdasarkan variance (spread values evenly)
$strategy4 = $sampleKpiData;
usort($strategy4, function ($a, $b) {
    return $a['realisasi'] <=> $b['realisasi'];
});

$evenSpread = [];
$sorted = $strategy4;
$indices = [0, 7, 1, 6, 2, 5, 3, 4]; // Spread pattern

foreach ($indices as $idx) {
    if (isset($sorted[$idx])) {
        $evenSpread[] = $sorted[$idx];
    }
}

$score4 = visualizeOrder($evenSpread, "STRATEGI 4: Even Spread Pattern");

// 5. Urutan berdasarkan status (Tercapai dulu, lalu Belum Tercapai)
$strategy5 = $sampleKpiData;
usort($strategy5, function ($a, $b) {
    // Urutkan berdasarkan status dulu
    if (($a['persentase'] >= 100) != ($b['persentase'] >= 100)) {
        return ($b['persentase'] >= 100) ? 1 : -1;
    }
    // Jika status sama, urutkan berdasarkan realisasi
    return $b['realisasi'] <=> $a['realisasi'];
});

$score5 = visualizeOrder($strategy5, "STRATEGI 5: Status-Based (Tercapai → Belum Tercapai)");

// 6. Urutan berdasarkan kategori KPI (IKT dulu, lalu PGB)
$strategy6 = $sampleKpiData;
usort($strategy6, function ($a, $b) {
    // Urutkan berdasarkan kategori dulu
    $aCategory = substr($a['kode'], 0, 3);
    $bCategory = substr($b['kode'], 0, 3);

    if ($aCategory != $bCategory) {
        return $aCategory <=> $bCategory; // IKT < PGB alphabetically
    }

    // Dalam kategori yang sama, urutkan berdasarkan realisasi
    return $b['realisasi'] <=> $a['realisasi'];
});

$score6 = visualizeOrder($strategy6, "STRATEGI 6: Category-Based (IKT → PGB, lalu Realisasi)");

// Comparison and recommendation
echo "=== ANALISIS PERBANDINGAN ===\n";
$strategies = [
    'Strategi 1 (Realisasi)' => $score1,
    'Strategi 2 (Persentase)' => $score2,
    'Strategi 3 (Alternating)' => $score3,
    'Strategi 4 (Even Spread)' => $score4,
    'Strategi 5 (Status-Based)' => $score5,
    'Strategi 6 (Category-Based)' => $score6
];

asort($strategies);

echo "🏆 RANKING BERDASARKAN SMOOTHNESS SCORE:\n";
$rank = 1;
foreach ($strategies as $strategy => $score) {
    $emoji = $rank == 1 ? '🥇' : ($rank == 2 ? '🥈' : ($rank == 3 ? '🥉' : '📊'));
    echo "$emoji $rank. $strategy: $score\n";
    $rank++;
}

$bestStrategy = array_key_first($strategies);
echo "\n✅ REKOMENDASI: $bestStrategy memiliki smoothness score terbaik!\n";

echo "\n=== KESIMPULAN ===\n";
echo "🎯 Untuk visualisasi radar chart yang optimal:\n";
echo "1. Pilih strategi dengan smoothness score terendah\n";
echo "2. Hindari lompatan nilai yang terlalu besar antar titik\n";
echo "3. Pertimbangkan aspek bisnis (status/kategori) vs visual smoothness\n";
echo "4. Test dengan data real untuk hasil yang lebih akurat\n";
