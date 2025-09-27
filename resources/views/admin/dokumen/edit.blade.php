@extends('admin.layouts.main')

@section('content')
    <div class="container-fluid">
        <h4 class="mb-3">Ubah Dokumen</h4>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.dokumen.update', $dokumen->id_dokumen) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Pengabdian</label>
                        <select name="id_pengabdian" class="form-select">
                            @foreach ($pengabdian as $p)
                                <option value="{{ $p->id_pengabdian }}" @if ($dokumen->id_pengabdian == $p->id_pengabdian) selected @endif>
                                    {{ $p->judul_pengabdian }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Dokumen</label>
                        <select name="id_jenis_dokumen" class="form-select">
                            @foreach ($jenisDokumen as $j)
                                <option value="{{ $j->id_jenis_dokumen }}"
                                    @if ($dokumen->id_jenis_dokumen == $j->id_jenis_dokumen) selected @endif>{{ $j->nama_jenis_dokumen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama File</label>
                        <input type="text" class="form-control" name="nama_file" value="{{ $dokumen->nama_file }}"
                            required>
                    </div>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                    <a href="{{ route('admin.dokumen.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
