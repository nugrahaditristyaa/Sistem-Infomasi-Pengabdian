<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailHki;
use Illuminate\Http\Request;

class HkiController extends Controller
{
    /**
     * Menampilkan daftar semua HKI.
     */
    public function index()
    {
        // Ambil semua data DetailHki beserta relasi yang diperlukan
        $query = DetailHki::with([
            'luaran.pengabdian.ketua',
            'dosen', // Dosen pencipta
            'dokumen'
        ])->latest('tgl_permohonan');

        // Opsional filter berdasarkan pengabdian
        if ($pengabdianId = request('pengabdian_id')) {
            $query->whereHas('luaran', function ($q) use ($pengabdianId) {
                $q->where('id_pengabdian', $pengabdianId);
            });
        }

        // Return all rows so DataTables (client-side) can handle pagination/filtering.
        $hki = $query->get();

        // tahunList untuk filter pada view (ambil tahun permohonan unik)
        $tahunList = DetailHki::selectRaw('YEAR(tgl_permohonan) as tahun')
            ->whereNotNull('tgl_permohonan')
            ->groupBy('tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun')->toArray();

        return view('admin.hki.index', compact('hki', 'tahunList'));
    }

    /**
     * Menampilkan detail satu HKI.
     */
    public function show($id)
    {
        // Ambil satu data DetailHki atau tampilkan error 404 jika tidak ditemukan
        $hki = DetailHki::with([
            'luaran.pengabdian.ketua',
            'luaran.pengabdian.dosen', // Semua dosen di pengabdian
            'dosen',
            'dokumen'
        ])->findOrFail($id);

        return view('admin.hki.show', compact('hki'));
    }
}
