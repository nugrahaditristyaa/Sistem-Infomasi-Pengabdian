<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Pengabdian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DosenRekapController extends Controller
{
    /**
     * Display dosen recap page with pengabdian activities (Admin view)
     */
    public function rekap(Request $request)
    {
        $currentYear = date('Y');
        $filterYear = $request->get('year', $currentYear);
        $filterProdi = $request->get('prodi', 'all');

        $availableYears = Pengabdian::selectRaw('YEAR(tanggal_pengabdian) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Apply FTI filter first (only Informatika and Sistem Informasi)
        $dosenQuery = Dosen::fti()->with(['pengabdian' => function ($query) use ($filterYear) {
            if ($filterYear !== 'all') {
                $query->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
            }
            $query->select('pengabdian.id_pengabdian', 'pengabdian.judul_pengabdian', 'pengabdian.tanggal_pengabdian')
                ->orderBy('pengabdian.tanggal_pengabdian', 'desc');
        }])
            ->withCount(['pengabdian as jumlah_pengabdian' => function ($query) use ($filterYear) {
                if ($filterYear !== 'all') {
                    $query->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
                }
            }]);

        if ($filterProdi !== 'all') {
            $dosenQuery->where('prodi', $filterProdi);
        }

        $dosenData = $dosenQuery->orderBy('jumlah_pengabdian', 'desc')
            ->paginate(20);

        // Get prodi options (only FTI)
        $prodiOptions = Dosen::fti()->select('prodi')
            ->distinct()
            ->orderBy('prodi')
            ->pluck('prodi');

        $routeBase = 'admin';
        $userRole = auth('admin')->user()->role ?? 'Admin';

        return view('admin.dosen.rekap', compact(
            'dosenData',
            'filterYear',
            'filterProdi',
            'availableYears',
            'prodiOptions',
            'routeBase',
            'userRole'
        ));
    }

    /**
     * Export dosen rekap to CSV for Admin
     */
    public function exportRekap(Request $request)
    {
        $filterYear = $request->get('year', date('Y'));
        $filterProdi = $request->get('prodi', 'all');

        // Apply FTI filter first (only Informatika and Sistem Informasi)
        $dosenQuery = Dosen::fti()->with(['pengabdian' => function ($query) use ($filterYear) {
            if ($filterYear !== 'all') {
                $query->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
            }
            $query->select('pengabdian.id_pengabdian', 'pengabdian.judul_pengabdian', 'pengabdian.tanggal_pengabdian')
                ->orderBy('pengabdian.tanggal_pengabdian', 'desc');
        }])
            ->withCount(['pengabdian as jumlah_pengabdian' => function ($query) use ($filterYear) {
                if ($filterYear !== 'all') {
                    $query->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
                }
            }]);

        if ($filterProdi !== 'all') {
            $dosenQuery->where('prodi', $filterProdi);
        }

        $dosenData = $dosenQuery->orderBy('jumlah_pengabdian', 'desc')->get();

        $filename = 'rekap_pengabdian_dosen_' . ($filterYear !== 'all' ? $filterYear : 'semua_tahun') . '_' . date('YmdHis') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($dosenData) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['No', 'Nama Dosen', 'NIK', 'NIDN', 'Program Studi', 'Bidang Keahlian', 'Jumlah Kegiatan', 'Judul Terlibat']);

            $no = 1;
            foreach ($dosenData as $dosen) {
                $judulTerlibat = $dosen->pengabdian->pluck('judul_pengabdian')->unique()->implode('; ');
                fputcsv($file, [
                    $no++,
                    $dosen->nama,
                    $dosen->nik,
                    $dosen->nidn ?? '-',
                    $dosen->prodi,
                    $dosen->bidang_keahlian ?? '-',
                    $dosen->jumlah_pengabdian,
                    $judulTerlibat ?: '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get detailed pengabdian data for a specific dosen (for modal/detail view)
     */
    public function dosenDetail(Request $request, $nik)
    {
        $filterYear = $request->get('year', date('Y'));

        $dosen = Dosen::with(['pengabdian' => function ($query) use ($filterYear) {
            if ($filterYear !== 'all') {
                $query->whereYear('tanggal_pengabdian', $filterYear);
            }
            $query->orderBy('tanggal_pengabdian', 'desc');
        }])->findOrFail($nik);

        if ($request->ajax()) {
            return response()->json([
                'dosen' => $dosen,
                'pengabdian' => $dosen->pengabdian->map(function ($pengabdian) {
                    return [
                        'id_pengabdian' => $pengabdian->id_pengabdian,
                        'judul' => $pengabdian->judul_pengabdian,
                        'tanggal_pengabdian' => $pengabdian->tanggal_pengabdian,
                        'status_anggota' => $pengabdian->pivot->status_anggota ?? 'Anggota',
                        'sumber_dana' => $pengabdian->sumberDana->nama_sumber ?? 'N/A'
                    ];
                })
            ]);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }
}
