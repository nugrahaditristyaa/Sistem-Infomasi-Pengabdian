<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Luaran;
use App\Models\Pengabdian;
use App\Models\KategoriSpmi;
use App\Models\JenisLuaran;
use Illuminate\Http\Request;

class LuaranController extends Controller
{
    public function index()
    {
        $luaran = Luaran::with(['pengabdian', 'kategoriSpmi', 'jenisLuaran'])->orderByDesc('id_luaran')->paginate(10);
        return view('admin.luaran.index', compact('luaran'));
    }

    public function create()
    {
        $pengabdian = Pengabdian::orderBy('judul_pengabdian')->get();
        $kategoriSpmi = KategoriSpmi::orderBy('kode_spmi')->get();
        $jenisLuaran = JenisLuaran::orderBy('nama_jenis_luaran')->get();
        return view('admin.luaran.create', compact('pengabdian', 'kategoriSpmi', 'jenisLuaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pengabdian' => 'required|exists:pengabdian,id_pengabdian',
            'id_kategori_spmi' => 'required|exists:kategori_spmi,id_kategori_spmi',
            'id_jenis_luaran' => 'required|exists:jenis_luaran,id_jenis_luaran',
            'judul' => 'required|string|max:255',
            'tahun' => 'required|integer|min:2000|max:2100',
        ]);

        Luaran::create($request->only(['id_pengabdian', 'id_kategori_spmi', 'id_jenis_luaran', 'judul', 'tahun']));
        return redirect()->route('admin.luaran.index')->with('success', 'Luaran ditambahkan');
    }

    public function show($id)
    {
        $luaran = Luaran::with(['pengabdian', 'kategoriSpmi', 'jenisLuaran', 'detailHki'])->findOrFail($id);
        return view('admin.luaran.show', compact('luaran'));
    }

    public function edit($id)
    {
        $luaran = Luaran::findOrFail($id);
        $pengabdian = Pengabdian::orderBy('judul_pengabdian')->get();
        $kategoriSpmi = KategoriSpmi::orderBy('kode_spmi')->get();
        $jenisLuaran = JenisLuaran::orderBy('nama_jenis_luaran')->get();
        return view('admin.luaran.edit', compact('luaran', 'pengabdian', 'kategoriSpmi', 'jenisLuaran'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_pengabdian' => 'required|exists:pengabdian,id_pengabdian',
            'id_kategori_spmi' => 'required|exists:kategori_spmi,id_kategori_spmi',
            'id_jenis_luaran' => 'required|exists:jenis_luaran,id_jenis_luaran',
            'judul' => 'required|string|max:255',
            'tahun' => 'required|integer|min:2000|max:2100',
        ]);

        $luaran = Luaran::findOrFail($id);
        $luaran->update($request->only(['id_pengabdian', 'id_kategori_spmi', 'id_jenis_luaran', 'judul', 'tahun']));
        return redirect()->route('admin.luaran.index')->with('success', 'Luaran diperbarui');
    }

    public function destroy($id)
    {
        $luaran = Luaran::findOrFail($id);
        $luaran->delete();
        return redirect()->route('admin.luaran.index')->with('success', 'Luaran dihapus');
    }
}
