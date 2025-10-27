<?php

namespace App\Http\Controllers\Dekan;

use App\Http\Controllers\Controller;
use App\Models\Kpi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DekanKpiController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index()
    {
        try {
            $kpis = Kpi::orderBy('kode', 'asc')->get();

            return view('dekan.kpi.index', compact('kpis'));
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

            return view('dekan.kpi.show', compact('kpi'));
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

            return view('dekan.kpi.edit', compact('kpi'));
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

            // Hanya izinkan perubahan pada target (angka)
            $request->validate([
                'target' => 'required|numeric|min:0',
            ], [
                'target.required' => 'Target wajib diisi.',
                'target.numeric' => 'Target harus berupa angka.',
                'target.min' => 'Target tidak boleh kurang dari 0.',
            ]);

            DB::beginTransaction();

            // Jangan ubah field lain
            $kpi->target = $request->target;
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
     * Update KPI by code (for AJAX modal form)
     */
    public function updateByCode(Request $request, string $kode)
    {
        try {
            $kpi = Kpi::where('kode', $kode)->firstOrFail();

            $request->validate([
                'target' => 'required|numeric|min:0',
            ], [
                'target.required' => 'Target (Angka) wajib diisi.',
                'target.numeric' => 'Target (Angka) harus berupa angka.',
                'target.min' => 'Target (Angka) tidak boleh kurang dari 0.',
            ]);

            DB::beginTransaction();

            // Hanya perbarui target dari modal; field lain tidak diubah
            $kpi->target = $request->target;
            $kpi->save();

            DB::commit();

            // Return JSON response for AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'KPI berhasil diperbarui.',
                    'data' => [
                        'kode' => $kpi->kode,
                        'indikator' => $kpi->indikator,
                        'target' => $kpi->target,
                        'satuan' => $kpi->satuan
                    ]
                ]);
            }

            return redirect()->route('inqa.kpi.index')
                ->with('success', 'KPI berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terdapat kesalahan dalam pengisian form.',
                    'errors' => $e->validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in InqaKpiController@updateByCode: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui KPI.'
                ], 500);
            }

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
