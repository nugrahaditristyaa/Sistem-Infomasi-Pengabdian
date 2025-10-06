<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DosenController extends Controller
{
    /**
     * Display a listing of the dosen.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $dosen = Dosen::all(); // atau Dosen::get();
        return view('admin.dosen.index', compact('dosen'));
    }

    /**
     * Show the form for creating a new dosen.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.dosen.create');
    }

    /**
     * Store a newly created dosen in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|max:8|unique:dosen,nik',
            'nama' => 'required|string|max:255',
            'nidn' => 'nullable|digits:10|unique:dosen,nidn',
            'jabatan' => 'nullable|string|max:100',
            'prodi' => 'required|string|max:100',
            'bidang_keahlian' => 'nullable|string|max:255',
            'email' => 'required|email|unique:dosen,email|max:255',
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus terdiri dari 8 digit.',
            'nik.unique' => 'NIK ini sudah terdaftar.',
            'nama.required' => 'Nama wajib diisi.',
            'nidn.digits' => 'NIDN harus 10 digit angka.',
            'nidn.unique' => 'NIDN ini sudah terdaftar.',
            'prodi.required' => 'Program studi wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
        ]);

        try {
            Dosen::create($request->all());
            return redirect()->route('admin.dosen.index')
                ->with('success', 'Data dosen berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified dosen.
     */
    public function show($nik)
    {
        $dosen = Dosen::with(['pengabdian', 'anggotaHki.detailHki.luaran.pengabdian'])
            ->findOrFail($nik);
        return view('admin.dosen.show', compact('dosen'));
    }

    /**
     * Show the form for editing the specified dosen.
     */
    public function edit($nik)
    {
        $dosen = Dosen::findOrFail($nik);
        return view('admin.dosen.edit', compact('dosen'));
    }

    /**
     * Update the specified dosen in storage.
     */
    public function update(Request $request, $nik)
    {
        $dosen = Dosen::findOrFail($nik);

        $request->validate([
            'nik' => [
                'required',
                'string',
                'max:8',
                Rule::unique('dosen', 'nik')->ignore($dosen->nik, 'nik'),
            ],
            'nama' => 'required|string|max:255',
            'nidn' => [
                'nullable',
                'digits:10',
                Rule::unique('dosen', 'nidn')->ignore($dosen->nik, 'nik'),
            ],
            'jabatan' => 'nullable|string|max:100',
            'prodi' => 'required|string|max:100',
            'bidang_keahlian' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('dosen', 'email')->ignore($dosen->nik, 'nik'),
            ],
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus 8 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar.',
            'nama.required' => 'Nama wajib diisi.',
            'nidn.digits' => 'NIDN harus 10 digit angka.',
            'nidn.unique' => 'NIDN ini sudah terdaftar.',
            'prodi.required' => 'Program studi wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
        ]);

        try {
            $dosen->update($request->all());

            return redirect()->route('admin.dosen.index')
                ->with('success', 'Data dosen berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified dosen from storage.
     */
    public function destroy($nik)
    {
        try {
            $dosen = Dosen::findOrFail($nik);
            $dosen->delete();

            return redirect()->route('admin.dosen.index')
                ->with('success', 'Data dosen berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
