<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the mahasiswa.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get all mahasiswa for DataTables to handle
        $mahasiswa = Mahasiswa::orderBy('nama')->get();
        return view('admin.mahasiswa.index', compact('mahasiswa'));
    }

    /**
     * Show the form for creating a new mahasiswa.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.mahasiswa.create');
    }

    /**
     * Store a newly created mahasiswa in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|unique:mahasiswa,nim|max:20',
            'nama' => 'required|string|max:255',
            'prodi' => 'required|string|max:100',
        ]);

        try {
            Mahasiswa::create($request->all());
            return redirect()->route('admin.mahasiswa.index')
                ->with('success', 'Data mahasiswa berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified mahasiswa.
     *
     * @param  string  $nim
     * @return \Illuminate\View\View
     */
    public function show($nim)
    {
        $mahasiswa = Mahasiswa::with(['pengabdian'])->findOrFail($nim);
        return view('admin.mahasiswa.show', compact('mahasiswa'));
    }

    /**
     * Show the form for editing the specified mahasiswa.
     *
     * @param  string  $nim
     * @return \Illuminate\View\View
     */
    public function edit($nim)
    {
        $mahasiswa = Mahasiswa::findOrFail($nim);
        return view('admin.mahasiswa.edit', compact('mahasiswa'));
    }

    /**
     * Update the specified mahasiswa in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $nim
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $nim)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'prodi' => 'required|string|max:100',
        ]);

        try {
            $mahasiswa = Mahasiswa::findOrFail($nim);
            $mahasiswa->update($request->all());

            return redirect()->route('admin.mahasiswa.index')
                ->with('success', 'Data mahasiswa berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified mahasiswa from storage.
     *
     * @param  string  $nim
     * @return \Illuminate\Http\Response
     */
    public function destroy($nim)
    {
        try {
            $mahasiswa = Mahasiswa::findOrFail($nim);
            $mahasiswa->delete();

            return redirect()->route('admin.mahasiswa.index')
                ->with('success', 'Data mahasiswa berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
