<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\Pengabdian;
use App\Models\JenisDokumen;
use Illuminate\Http\Request;

class DokumenController extends Controller
{
    public function index()
    {
        $dokumen = Dokumen::with(['pengabdian', 'jenisDokumen'])->orderByDesc('id_dokumen')->paginate(10);
        return view('admin.dokumen.index', compact('dokumen'));
    }

    public function create()
    {
        $pengabdian = Pengabdian::orderBy('judul_pengabdian')->get();
        $jenisDokumen = JenisDokumen::orderBy('nama_jenis_dokumen')->get();
        return view('admin.dokumen.create', compact('pengabdian', 'jenisDokumen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pengabdian' => 'required|exists:pengabdian,id_pengabdian',
            'id_jenis_dokumen' => 'required|exists:jenis_dokumen,id_jenis_dokumen',
            'nama_file' => 'required|string|max:255',
        ]);

        Dokumen::create($request->only(['id_pengabdian', 'id_jenis_dokumen', 'nama_file']));
        return redirect()->route('admin.dokumen.index')->with('success', 'Dokumen ditambahkan');
    }

    public function show($id)
    {
        $dokumen = Dokumen::with(['pengabdian', 'jenisDokumen'])->findOrFail($id);
        return view('admin.dokumen.show', compact('dokumen'));
    }

    public function edit($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        $pengabdian = Pengabdian::orderBy('judul_pengabdian')->get();
        $jenisDokumen = JenisDokumen::orderBy('nama_jenis_dokumen')->get();
        return view('admin.dokumen.edit', compact('dokumen', 'pengabdian', 'jenisDokumen'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_pengabdian' => 'required|exists:pengabdian,id_pengabdian',
            'id_jenis_dokumen' => 'required|exists:jenis_dokumen,id_jenis_dokumen',
            'nama_file' => 'required|string|max:255',
        ]);

        $dokumen = Dokumen::findOrFail($id);
        $dokumen->update($request->only(['id_pengabdian', 'id_jenis_dokumen', 'nama_file']));
        return redirect()->route('admin.dokumen.index')->with('success', 'Dokumen diperbarui');
    }

    public function destroy($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        $dokumen->delete();
        return redirect()->route('admin.dokumen.index')->with('success', 'Dokumen dihapus');
    }
}
