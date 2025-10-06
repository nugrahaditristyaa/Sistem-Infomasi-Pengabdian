<?php

namespace App\Http\Controllers\Inqa;

use App\Http\Controllers\Controller;
use App\Models\Kpi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InqaKpiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $kpis = Kpi::orderBy('kode', 'asc')->get();

            return view('inqa.kpi.index', compact('kpis'));
        } catch (\Exception $e) {
            Log::error('Error in InqaKpiController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data KPI.');
        }
    }

    /**
     * Store a newly created resource in storage.
     * Note: InQA role is not allowed to create new KPI
     */
    public function store(Request $request)
    {
        return redirect()->route('inqa.kpi.index')
            ->with('error', 'Akses ditolak: InQA tidak memiliki hak untuk menambahkan KPI baru.');
    }

    /**
     * Show the form for creating a new resource.
     * Note: InQA role is not allowed to create new KPI
     */
    public function create()
    {
        return redirect()->route('inqa.kpi.index')
            ->with('error', 'Akses ditolak: InQA tidak memiliki hak untuk menambahkan KPI baru.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $kpi = Kpi::findOrFail($id);

            return view('inqa.kpi.show', compact('kpi'));
        } catch (\Exception $e) {
            Log::error('Error in InqaKpiController@show: ' . $e->getMessage());
            return redirect()->route('inqa.kpi.index')->with('error', 'KPI tidak ditemukan.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $kpi = Kpi::findOrFail($id);

            return view('inqa.kpi.edit', compact('kpi'));
        } catch (\Exception $e) {
            Log::error('Error in InqaKpiController@edit: ' . $e->getMessage());
            return redirect()->route('inqa.kpi.index')->with('error', 'KPI tidak ditemukan.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $kpi = Kpi::findOrFail($id);

            $request->validate([
                'nama_indikator' => 'required|string|max:255',
                'target' => 'required|numeric|min:0',
                'satuan' => 'required|string|max:50',
            ], [
                'nama_indikator.required' => 'Nama indikator wajib diisi.',
                'nama_indikator.max' => 'Nama indikator maksimal 255 karakter.',
                'target.required' => 'Target wajib diisi.',
                'target.numeric' => 'Target harus berupa angka.',
                'target.min' => 'Target tidak boleh kurang dari 0.',
                'satuan.required' => 'Satuan wajib diisi.',
                'satuan.max' => 'Satuan maksimal 50 karakter.',
            ]);

            DB::beginTransaction();

            $kpi->nama_indikator = trim($request->nama_indikator);
            $kpi->target = $request->target;
            $kpi->satuan = trim($request->satuan);
            $kpi->save();

            DB::commit();

            return redirect()->route('inqa.kpi.index')
                ->with('success', 'KPI berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in InqaKpiController@update: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui KPI.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return redirect()->route('inqa.kpi.index')
            ->with('error', 'Akses ditolak: InQA tidak memiliki hak untuk menghapus KPI.');
    }
}
