<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Kpi;

echo "=== TESTING SMOOTH TRANSITION STRATEGY ===\n\n";

// Simulasi data KPI dengan berbagai skenario
$scenarios = [
    'Normal Data' => [
        ['kode' => 'KPI-A', 'realisasi' => 90, 'persentase' => 100],
        ['kode' => 'KPI-B', 'realisasi' => 85, 'persentase' => 100],
        ['kode' => 'KPI-C', 'realisasi' => 75, 'persentase' => 100],
        ['kode' => 'KPI-D', 'realisasi' => 45, 'persentase' => 65],
        ['kode' => 'KPI-E', 'realisasi' => 20, 'persentase' => 68],
        ['kode' => 'KPI-F', 'realisasi' => 15, 'persentase' => 100],
        ['kode' => 'KPI-G', 'realisasi' => 8, 'persentase' => 87],
        ['kode' => 'KPI-H', 'realisasi' => 5, 'persentase' => 100]
    ],
    'Extreme Gaps Data' => [
        ['kode' => 'KPI-A', 'realisasi' => 95, 'persentase' => 100],
        ['kode' => 'KPI-B', 'realisasi' => 2, 'persentase' => 100],
        ['kode' => 'KPI-C', 'realisasi' => 80, 'persentase' => 100],
        ['kode' => 'KPI-D', 'realisasi' => 1, 'persentase' => 50],
        ['kode' => 'KPI-E', 'realisasi' => 70, 'persentase' => 88],
        ['kode' => 'KPI-F', 'realisasi' => 3, 'persentase' => 100]
    ]
];

function calculateMetrics($data)
{
    $realisasiValues = array_column($data, 'realisasi');
    $jumps = [];
    $largeJumps = 0;

    for ($i = 0; $i < count($realisasiValues) - 1; $i++) {
        $jump = abs($realisasiValues[$i] - $realisasiValues[$i + 1]);
        $jumps[] = $jump;
        if ($jump > 50) $largeJumps++;
    }

    return [
        'max_jump' => max($jumps),
        'avg_jump' => array_sum($jumps) / count($jumps),
        'large_jumps' => $largeJumps,
        'smoothness_score' => array_sum($jumps) + ($largeJumps * 25) // Penalti untuk lompatan besar
    ];
}

function testStrategy($data, $strategyName)
{
    echo "--- Testing: $strategyName ---\n";

    // Original strategy (simple sort by realisasi)
    $original = $data;
    usort($original, function ($a, $b) {
        return $b['realisasi'] <=> $a['realisasi'];
    });

    echo "ORIGINAL SORT:\n";
    foreach ($original as $i => $kpi) {
        echo sprintf("%d. %s: %.1f\n", $i + 1, $kpi['kode'], $kpi['realisasi']);
    }
    $originalMetrics = calculateMetrics($original);

    // Smooth transition strategy 
    $optimized = [];
    $remaining = $original;

    // Mulai dengan tertinggi
    $optimized[] = array_shift($remaining);

    // Cari yang terdekat untuk setiap step
    while (!empty($remaining)) {
        $lastValue = end($optimized)['realisasi'];
        $bestIndex = 0;
        $bestDiff = abs($remaining[0]['realisasi'] - $lastValue);

        for ($i = 1; $i < count($remaining); $i++) {
            $diff = abs($remaining[$i]['realisasi'] - $lastValue);
            if ($diff < $bestDiff || ($bestDiff > 50 && $diff < 50)) {
                $bestDiff = $diff;
                $bestIndex = $i;
            }
        }

        $optimized[] = $remaining[$bestIndex];
        array_splice($remaining, $bestIndex, 1);
    }

    echo "\nSMOOTH TRANSITION:\n";
    foreach ($optimized as $i => $kpi) {
        echo sprintf("%d. %s: %.1f\n", $i + 1, $kpi['kode'], $kpi['realisasi']);
    }
    $optimizedMetrics = calculateMetrics($optimized);

    // Comparison
    echo "\nCOMPARISON:\n";
    echo sprintf(
        "Max Jump: %.1f → %.1f (%s)\n",
        $originalMetrics['max_jump'],
        $optimizedMetrics['max_jump'],
        $optimizedMetrics['max_jump'] < $originalMetrics['max_jump'] ? '✅ Better' : '❌ Worse'
    );

    echo sprintf(
        "Avg Jump: %.1f → %.1f (%s)\n",
        $originalMetrics['avg_jump'],
        $optimizedMetrics['avg_jump'],
        $optimizedMetrics['avg_jump'] < $originalMetrics['avg_jump'] ? '✅ Better' : '❌ Worse'
    );

    echo sprintf(
        "Large Jumps (>50): %d → %d (%s)\n",
        $originalMetrics['large_jumps'],
        $optimizedMetrics['large_jumps'],
        $optimizedMetrics['large_jumps'] < $originalMetrics['large_jumps'] ? '✅ Better' : '❌ Worse'
    );

    echo sprintf(
        "Smoothness Score: %.1f → %.1f (%s)\n",
        $originalMetrics['smoothness_score'],
        $optimizedMetrics['smoothness_score'],
        $optimizedMetrics['smoothness_score'] < $originalMetrics['smoothness_score'] ? '✅ Better' : '❌ Worse'
    );

    echo "\n" . str_repeat("=", 50) . "\n\n";

    return [
        'original' => $originalMetrics,
        'optimized' => $optimizedMetrics,
        'improvement' => $optimizedMetrics['smoothness_score'] < $originalMetrics['smoothness_score']
    ];
}

// Test all scenarios
$results = [];
foreach ($scenarios as $name => $data) {
    $results[$name] = testStrategy($data, $name);
}

// Summary
echo "=== OVERALL SUMMARY ===\n";
$improved = 0;
$total = count($results);

foreach ($results as $scenario => $result) {
    $status = $result['improvement'] ? '✅ Improved' : '❌ No improvement';
    echo "$scenario: $status\n";
    if ($result['improvement']) $improved++;
}

echo "\nImprovement Rate: $improved/$total scenarios\n";

if ($improved > $total / 2) {
    echo "🎯 RECOMMENDATION: Implement Smooth Transition Strategy\n";
    echo "   This strategy reduces extreme jumps and creates smoother radar visualization.\n";
} else {
    echo "🤔 RECOMMENDATION: Keep original simple sorting\n";
    echo "   Smooth transition doesn't provide significant improvement for this dataset.\n";
}
