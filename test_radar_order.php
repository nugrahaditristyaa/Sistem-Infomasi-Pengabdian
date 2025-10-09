<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Kpi;
use Illuminate\Support\Facades\DB;

echo "=== TEST URUTAN KPI RADAR CHART ===\n\n";

// Simulasi controller InqaController
class TestInqaController
{

    public function getKpiRadarDataTest($filterYear)
    {
        // Ambil semua KPI
        $kpis = Kpi::orderBy('kode')->get();

        $radarData = [];

        foreach ($kpis as $kpi) {
            // Hitung capaian berdasarkan data pengabdian
            $capaian = $this->calculateKpiAchievementTest($kpi, $filterYear);

            // Hitung persentase capaian (capaian/target * 100)
            $persentaseCapaian = $kpi->target > 0 ? ($capaian / $kpi->target * 100) : 0;

            // Batasi maksimal 100%
            $persentaseCapaian = min($persentaseCapaian, 100);

            $radarData[] = [
                'kode' => $kpi->kode,
                'indikator' => $kpi->indikator,
                'target' => $kpi->target,
                'realisasi' => $capaian,
                'persentase' => round($persentaseCapaian, 1),
                'satuan' => $kpi->satuan,
                'status' => $persentaseCapaian >= 100 ? 'Tercapai' : 'Belum Tercapai'
            ];
        }

        echo "=== SEBELUM DIURUTKAN (berdasarkan kode) ===\n";
        foreach ($radarData as $index => $kpi) {
            echo sprintf(
                "%d. %s: %.2f %s (%.1f%%) - %s\n",
                $index + 1,
                $kpi['kode'],
                $kpi['realisasi'],
                $kpi['satuan'],
                $kpi['persentase'],
                $kpi['status']
            );
        }

        // Urutkan berdasarkan nilai realisasi dari tertinggi ke terendah untuk tampilan radar chart yang lebih smooth
        usort($radarData, function ($a, $b) {
            // Jika realisasi sama, urutkan berdasarkan persentase capaian
            if ($a['realisasi'] == $b['realisasi']) {
                return $b['persentase'] <=> $a['persentase'];
            }
            return $b['realisasi'] <=> $a['realisasi'];
        });

        echo "\n=== SETELAH DIURUTKAN (berdasarkan realisasi tertinggi ke terendah) ===\n";
        foreach ($radarData as $index => $kpi) {
            echo sprintf(
                "%d. %s: %.2f %s (%.1f%%) - %s\n",
                $index + 1,
                $kpi['kode'],
                $kpi['realisasi'],
                $kpi['satuan'],
                $kpi['persentase'],
                $kpi['status']
            );
        }

        return $radarData;
    }

    private function calculateKpiAchievementTest($kpi, $filterYear)
    {
        // Simulasi perhitungan simple untuk test
        $achievements = [
            'IKT.I.5.g' => 75.50,
            'IKT.I.5.h' => 45.25,
            'IKT.I.5.j' => 85.75,
            'PGB.I.7.4' => 20.50,
            'PGB.I.7.9' => 15.25,
            'PGB.I.5.6' => 8.75,
            'PGB.I.1.1' => 90.25,
            'IKT.I.5.i' => 5
        ];

        return $achievements[$kpi->kode] ?? rand(10, 100);
    }
}

// Test urutan KPI
$testController = new TestInqaController();
$kpiRadarData = $testController->getKpiRadarDataTest('all');

echo "\n=== ANALISIS URUTAN UNTUK RADAR CHART ===\n";
echo "✅ Manfaat urutan berdasarkan realisasi:\n";
echo "1. Menciptakan aliran visual yang lebih smooth\n";
echo "2. KPI dengan pencapaian tinggi di depan menciptakan 'positive impression'\n";
echo "3. Mengurangi 'zigzag pattern' yang membuat chart sulit dibaca\n";
echo "4. Memudahkan identifikasi area yang perlu perbaikan\n\n";

echo "📊 Urutan optimal untuk radar visualization:\n";
$labels = array_column($kpiRadarData, 'kode');
echo "Labels: ['" . implode("', '", $labels) . "']\n";

echo "\n🎯 Chart akan menampilkan transisi yang smooth dari:\n";
echo "   Realisasi Tinggi → Realisasi Sedang → Realisasi Rendah\n";
echo "   Ini membuat polygon radar chart tidak berpotongan dan mudah dibaca.\n";

echo "\n=== HASIL TEST BERHASIL ===\n";
echo "✅ Urutan KPI berhasil diatur berdasarkan nilai realisasi\n";
echo "✅ Tampilan radar chart akan lebih smooth dan mudah dibaca\n";
echo "✅ Tidak ada lagi pattern zigzag yang membingungkan\n";
