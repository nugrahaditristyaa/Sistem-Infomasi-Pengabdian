<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kpi;
use App\Models\MonitoringKpi;
use App\Models\Pengabdian;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    /**
     * Display a listing of the KPI.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $kpi = Kpi::orderBy('kode')->paginate(10);
        return view('InQA.kpi.index', compact('kpi'));
    }

    /**
     * Show the form for creating a new KPI.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.kpi.create');
    }

    /**
     * Store a newly created KPI in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|unique:kpi,kode|max:50',
            'nama_indikator' => 'required|string|max:255',
            'target' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
        ]);

        try {
            Kpi::create($request->all());
            return redirect()->route('admin.kpi.index')
                ->with('success', 'Data KPI berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified KPI.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $kpi = Kpi::with(['monitoringKpi.pengabdian'])->findOrFail($id);
        return view('admin.kpi.show', compact('kpi'));
    }

    /**
     * Show the form for editing the specified KPI.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $kpi = Kpi::findOrFail($id);
        return view('admin.kpi.edit', compact('kpi'));
    }

    /**
     * Update the specified KPI in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:kpi,kode,' . $id . ',id_kpi',
            'indikator' => 'required|string|max:255',
            'target' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
        ]);

        try {
            $kpi = Kpi::findOrFail($id);
            $kpi->update($request->all());

            return redirect()->route('admin.kpi.index')
                ->with('success', 'Data KPI berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified KPI from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $kpi = Kpi::findOrFail($id);
            $kpi->delete();

            return redirect()->route('admin.kpi.index')
                ->with('success', 'Data KPI berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show monitoring KPI form.
     *
     * @return \Illuminate\View\View
     */
    public function monitoring()
    {
        $kpi = Kpi::orderBy('kode')->get();
        $pengabdian = Pengabdian::orderBy('judul_pengabdian')->get();
        $monitoring = MonitoringKpi::with(['kpi', 'pengabdian'])
            ->orderBy('tahun', 'desc')
            ->paginate(15);

        return view('admin.kpi.monitoring', compact('kpi', 'pengabdian', 'monitoring'));
    }

    /**
     * Store monitoring KPI data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeMonitoring(Request $request)
    {
        $request->validate([
            'id_kpi' => 'required|exists:kpi,id_kpi',
            'id_pengabdian' => 'required|exists:pengabdian,id_pengabdian',
            'tahun' => 'required|integer|min:2020|max:2030',
            'nilai_capai' => 'required|integer|min:0',
            'status' => 'nullable|string|max:50',
        ]);

        try {
            MonitoringKpi::create($request->all());
            return redirect()->route('admin.kpi.monitoring')
                ->with('success', 'Data monitoring KPI berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
