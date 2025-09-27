<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengabdian;
use App\Models\Dosen;
use App\Models\Luaran;
use App\Models\JenisLuaran;
use App\Models\JenisDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 1. Latest pengabdian (used by dashboard view)
        // Eager-load dokumen and the dokumen's jenisDokumen for per-type status
        // Show the most recently added pengabdian (by created_at)
        $latestPengabdian = Pengabdian::with(['ketua', 'dokumen.jenisDokumen'])
            ->latest('created_at')
            ->take(5)
            ->get();

        // 2. Quick statistics
        $currentYear = date('Y');
        $previousYear = $currentYear - 1;

        $totalPengabdian = Pengabdian::count();
        $totalPengabdianThisYear = Pengabdian::whereYear('tanggal_pengabdian', $currentYear)->count();
        $totalPengabdianLastYear = Pengabdian::whereYear('tanggal_pengabdian', $previousYear)->count();

        // Hitung persentase perubahan
        $percentageChangePengabdian = $totalPengabdianLastYear > 0 ?
            round((($totalPengabdianThisYear - $totalPengabdianLastYear) / $totalPengabdianLastYear) * 100, 1) : 0;

        $totalDosenTerlibat = DB::table('pengabdian_dosen')->distinct('nik')->count('nik');

        // Untuk dosen, hitung perbandingan tahun ini vs tahun lalu
        $dosenTerlibatThisYear = DB::table('pengabdian_dosen')
            ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
            ->whereYear('pengabdian.tanggal_pengabdian', $currentYear)
            ->distinct('pengabdian_dosen.nik')
            ->count('pengabdian_dosen.nik');

        $dosenTerlibatLastYear = DB::table('pengabdian_dosen')
            ->join('pengabdian', 'pengabdian_dosen.id_pengabdian', '=', 'pengabdian.id_pengabdian')
            ->whereYear('pengabdian.tanggal_pengabdian', $previousYear)
            ->distinct('pengabdian_dosen.nik')
            ->count('pengabdian_dosen.nik');

        $percentageChangeDosen = $dosenTerlibatLastYear > 0 ?
            round((($dosenTerlibatThisYear - $dosenTerlibatLastYear) / $dosenTerlibatLastYear) * 100, 1) : 0;

        $totalMahasiswaTerlibat = DB::table('pengabdian_mahasiswa')->distinct('nim')->count('nim');
        $pengabdianDenganMahasiswa = Pengabdian::whereHas('mahasiswa')->count();
        $persentasePengabdianDenganMahasiswa = $totalPengabdian > 0 ?
            round(($pengabdianDenganMahasiswa / $totalPengabdian) * 100, 1) : 0;

        // Perbandingan tahun ini vs tahun lalu untuk pengabdian dengan mahasiswa
        $pengabdianDenganMahasiswaThisYear = Pengabdian::whereYear('tanggal_pengabdian', $currentYear)
            ->whereHas('mahasiswa')->count();
        $pengabdianDenganMahasiswaLastYear = Pengabdian::whereYear('tanggal_pengabdian', $previousYear)
            ->whereHas('mahasiswa')->count();

        $percentageChangeMahasiswa = $pengabdianDenganMahasiswaLastYear > 0 ?
            round((($pengabdianDenganMahasiswaThisYear - $pengabdianDenganMahasiswaLastYear) / $pengabdianDenganMahasiswaLastYear) * 100, 1) : 0;

        $stats = [
            'total_pengabdian' => $totalPengabdian,
            'total_dosen' => $totalDosenTerlibat,
            'total_mahasiswa' => $pengabdianDenganMahasiswa,
            'current_year' => $currentYear,
            'previous_year' => $previousYear,
            'percentage_change_pengabdian' => $percentageChangePengabdian,
            'percentage_change_dosen' => $percentageChangeDosen,
            'percentage_change_mahasiswa' => $percentageChangeMahasiswa,
            'persentase_pengabdian_dengan_mahasiswa' => $persentasePengabdianDenganMahasiswa,
        ];

        // 3. Tasks / actions for "Perlu Tindakan"
        // Determine the id for 'Laporan Akhir' dynamically (fallback to null if not found)
        $laporanAkhir = JenisDokumen::where('nama_jenis_dokumen', 'Laporan Akhir')->first();
        $laporanAkhirId = $laporanAkhir ? $laporanAkhir->id_jenis_dokumen : null;

        if ($laporanAkhirId) {
            $tanpaLaporanAkhir = Pengabdian::whereDoesntHave('dokumen', function ($q) use ($laporanAkhirId) {
                $q->where('id_jenis_dokumen', $laporanAkhirId);
            })->count();
        } else {
            // If jenis dokumen not present in DB, fallback to count pengabdian without any dokumen
            $tanpaLaporanAkhir = Pengabdian::whereDoesntHave('dokumen')->count();
        }

        $hkiTanpaDokumen = Luaran::whereHas('jenisLuaran', function ($q) {
            $q->where('nama_jenis_luaran', 'HKI');
        })
            ->whereDoesntHave('detailHki.dokumen')
            ->count();

        $tasks = [
            [
                'label' => "$tanpaLaporanAkhir Pengabdian belum ada Laporan Akhir",
                'count' => $tanpaLaporanAkhir,
                'url' => route('admin.pengabdian.index', ['filter' => 'no_report'])
            ],
            [
                'label' => "$hkiTanpaDokumen Luaran HKI belum ada dokumen",
                'count' => $hkiTanpaDokumen,
                'url' => route('admin.hki.index', ['filter' => 'no_doc'])
            ],
        ];

        // Additional data for charts (kept for backward compatibility with the Blade)
        // Data for document completeness chart
        $pengabdianLengkap = $totalPengabdian - $tanpaLaporanAkhir;

        // Hitung total pengabdian (sebagai ketua + anggota) untuk setiap dosen
        $dosenCounts = Dosen::withCount('pengabdian as jumlah_pengabdian')
            ->orderBy('jumlah_pengabdian', 'desc')
            ->get(); // Anda bisa menambahkan ->take(5) jika hanya ingin top 5

        $namaDosen = $dosenCounts->pluck('nama');
        $jumlahPengabdianDosen = $dosenCounts->pluck('jumlah_pengabdian');

        // Treemap data for Luaran
        $luaranCounts = JenisLuaran::withCount('luaran')->get();
        $dataTreemap = $luaranCounts->map(function ($item) {
            return ['g' => $item->nama_jenis_luaran, 'v' => $item->luaran_count];
        })->toArray();

        // Get list of document types for per-pengabdian status display
        $jenisDokumenList = JenisDokumen::orderBy('nama_jenis_dokumen')->get();

        // Determine completeness for each of the latest pengabdian based on required document names
        $requiredDocNames = [
            'Surat Tugas Dosen',
            'Surat Permohonan',
            'Surat Ucapan Terima Kasih',
            'MoU/MoA/Dokumen Kerja Sama Kegiatan',
            'Laporan Akhir',
        ];

        $requiredJenis = JenisDokumen::whereIn('nama_jenis_dokumen', $requiredDocNames)->get()->keyBy('nama_jenis_dokumen');

        $completenessMap = [];
        foreach ($latestPengabdian as $p) {
            $present = 0;
            foreach ($requiredDocNames as $name) {
                if (isset($requiredJenis[$name])) {
                    $id = $requiredJenis[$name]->id_jenis_dokumen;
                    if ($p->dokumen->contains('id_jenis_dokumen', $id)) {
                        $present++;
                    }
                }
                // If the jenisDokumen is not seeded, we treat it as missing (so completeness will be false)
            }
            $isComplete = ($present === count($requiredDocNames));
            $completenessMap[$p->id_pengabdian] = $isComplete;
        }

        // Build a list of all Pengabdian that are missing one or more required documents
        $allPengabdian = Pengabdian::with(['ketua', 'dokumen.jenisDokumen'])->get();
        $pengabdianNeedingDocs = [];
        foreach ($allPengabdian as $p) {
            $missing = [];
            foreach ($requiredDocNames as $name) {
                if (isset($requiredJenis[$name])) {
                    $id = $requiredJenis[$name]->id_jenis_dokumen;
                    if (! $p->dokumen->contains('id_jenis_dokumen', $id)) {
                        $missing[] = $name;
                    }
                } else {
                    // If jenisDokumen not found in DB, consider it missing
                    $missing[] = $name;
                }
            }
            if (count($missing) > 0) {
                $pengabdianNeedingDocs[] = [
                    'id' => $p->id_pengabdian,
                    'judul' => $p->judul_pengabdian,
                    'ketua' => $p->ketua->nama ?? '-',
                    'missing' => $missing,
                ];
            }
        }

        $needActionCount = count($pengabdianNeedingDocs);

        // Compute counts per required document name
        $missingCounts = array_fill_keys($requiredDocNames, 0);
        foreach ($pengabdianNeedingDocs as $p) {
            foreach ($p['missing'] as $m) {
                if (isset($missingCounts[$m])) {
                    $missingCounts[$m]++;
                } else {
                    // In case a missing name isn't in requiredDocNames (unlikely), add it
                    $missingCounts[$m] = 1;
                }
            }
        }

        // Return the dashboard view with variables the Blade expects
        return view('admin.dashboard', compact(
            'latestPengabdian',
            'tasks',
            'stats',
            'totalPengabdian',
            'totalDosenTerlibat',
            'totalMahasiswaTerlibat',
            'pengabdianDenganMahasiswa',
            'laporanAkhirId',
            'pengabdianLengkap',
            'tanpaLaporanAkhir',
            'hkiTanpaDokumen',
            'namaDosen',
            'jumlahPengabdianDosen',
            'dataTreemap',
            'jenisDokumenList',
            'completenessMap',
            'pengabdianNeedingDocs',
            'needActionCount',
            'missingCounts'
        ));
    }
}
