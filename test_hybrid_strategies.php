<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== OPTIMASI LANJUTAN: HYBRID STRATEGIES ===\n\n";

// Sample data KPI 
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

function calculateSmoothness($data)
{
    $score = 0;
    $realisasiValues = array_column($data, 'realisasi');

    for ($i = 0; $i < count($realisasiValues) - 1; $i++) {
        $diff = abs($realisasiValues[$i] - $realisasiValues[$i + 1]);
        $score += $diff;

        // Penalti tambahan untuk lompatan besar
        if ($diff > 40) {
            $score += $diff * 0.5;
        }
        if ($diff > 70) {
            $score += $diff * 1.0; // Penalti berat
        }
    }

    return $score;
}

function visualizeStrategy($data, $title)
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
    echo "📊 Smoothness Score: $smoothness\n";

    // Analisis lompatan
    $realisasiValues = array_column($data, 'realisasi');
    $jumps = [];
    for ($i = 0; $i < count($realisasiValues) - 1; $i++) {
        $jump = abs($realisasiValues[$i] - $realisasiValues[$i + 1]);
        $jumps[] = $jump;
    }
    $maxJump = max($jumps);
    $avgJump = array_sum($jumps) / count($jumps);

    echo "📈 Max Jump: $maxJump, Avg Jump: " . round($avgJump, 2) . "\n";
    echo "📋 Labels: ['" . implode("', '", $labels) . "']\n\n";

    return $smoothness;
}

// HYBRID STRATEGY 1: Graduated Descent (mengurangi lompatan besar)
$hybrid1 = $sampleKpiData;
usort($hybrid1, function ($a, $b) {
    return $b['realisasi'] <=> $a['realisasi'];
});

// Reorder untuk mengurangi gap besar
$optimized1 = [];
$remaining = $hybrid1;

// Ambil yang tertinggi
$optimized1[] = array_shift($remaining);

while (!empty($remaining)) {
    $lastValue = end($optimized1)['realisasi'];

    // Cari yang paling dekat nilainya
    $bestIndex = 0;
    $bestDiff = abs($remaining[0]['realisasi'] - $lastValue);

    for ($i = 1; $i < count($remaining); $i++) {
        $diff = abs($remaining[$i]['realisasi'] - $lastValue);
        if ($diff < $bestDiff) {
            $bestDiff = $diff;
            $bestIndex = $i;
        }
    }

    $optimized1[] = $remaining[$bestIndex];
    array_splice($remaining, $bestIndex, 1);
}

$score_hybrid1 = visualizeStrategy($optimized1, "HYBRID 1: Graduated Descent (Minimal Gaps)");

// HYBRID STRATEGY 2: Balanced Groups (kelompokkan berdasarkan range)
$hybrid2 = $sampleKpiData;

// Kategorikan berdasarkan range nilai
$high = []; // >70
$medium = []; // 20-70
$low = []; // <20

foreach ($hybrid2 as $kpi) {
    if ($kpi['realisasi'] > 70) {
        $high[] = $kpi;
    } elseif ($kpi['realisasi'] >= 20) {
        $medium[] = $kpi;
    } else {
        $low[] = $kpi;
    }
}

// Urutkan masing-masing grup
usort($high, function ($a, $b) {
    return $b['realisasi'] <=> $a['realisasi'];
});
usort($medium, function ($a, $b) {
    return $b['realisasi'] <=> $a['realisasi'];
});
usort($low, function ($a, $b) {
    return $b['realisasi'] <=> $a['realisasi'];
});

$balanced = array_merge($high, $medium, $low);

$score_hybrid2 = visualizeStrategy($balanced, "HYBRID 2: Balanced Groups (High → Medium → Low)");

// HYBRID STRATEGY 3: Circular Optimization (urutkan seperti jam)
$hybrid3 = $sampleKpiData;
usort($hybrid3, function ($a, $b) {
    return $b['realisasi'] <=> $a['realisasi'];
});

// Arrangement untuk circular smoothness
$circular = [];
$values = $hybrid3;

// Mulai dari tengah-atas (12 o'clock)
$circular[] = array_shift($values); // Tertinggi di atas

// Distribusi ke kanan dan kiri secara bergantian
$right = [];
$left = [];

for ($i = 0; $i < count($values); $i++) {
    if ($i % 2 == 0) {
        $right[] = $values[$i];
    } else {
        $left[] = $values[$i];
    }
}

// Gabungkan: tengah → kanan → bawah → kiri
$left = array_reverse($left);
$result = array_merge($circular, $right, $left);

$score_hybrid3 = visualizeStrategy($result, "HYBRID 3: Circular Optimization");

// HYBRID STRATEGY 4: Smart Positioning (berdasarkan target dan realisasi)
$hybrid4 = $sampleKpiData;

// Hitung rasio realisasi/target untuk prioritas
foreach ($hybrid4 as &$kpi) {
    $kpi['ratio'] = $kpi['target'] > 0 ? $kpi['realisasi'] / $kpi['target'] : 0;
    $kpi['score'] = $kpi['realisasi'] * 0.7 + $kpi['persentase'] * 0.3; // Weighted score
}

usort($hybrid4, function ($a, $b) {
    return $b['score'] <=> $a['score'];
});

$score_hybrid4 = visualizeStrategy($hybrid4, "HYBRID 4: Smart Positioning (Weighted Score)");

// COMPARISON
echo "=== FINAL COMPARISON ===\n";
$strategies = [
    'Original (Realisasi)' => 85.25, // from previous test
    'Hybrid 1 (Graduated)' => $score_hybrid1,
    'Hybrid 2 (Balanced)' => $score_hybrid2,
    'Hybrid 3 (Circular)' => $score_hybrid3,
    'Hybrid 4 (Smart)' => $score_hybrid4
];

asort($strategies);

echo "🏆 FINAL RANKING:\n";
$rank = 1;
foreach ($strategies as $strategy => $score) {
    $emoji = $rank == 1 ? '🥇' : ($rank == 2 ? '🥈' : ($rank == 3 ? '🥉' : '📊'));
    echo "$emoji $rank. $strategy: $score\n";
    $rank++;
}

$bestStrategy = array_key_first($strategies);
echo "\n🎯 WINNER: $bestStrategy\n";

echo "\n=== IMPLEMENTATION RECOMMENDATION ===\n";
echo "Berdasarkan analisis, implementasikan strategi terbaik ke dalam controller.\n";
