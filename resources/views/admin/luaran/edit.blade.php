@extends('admin.layouts.main')

@section('content')
    <div class="container-fluid">
        <h4 class="mb-3">Ubah Luaran</h4>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.luaran.update', $luaran->id_luaran) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Pengabdian</label>
                        <select name="id_pengabdian" class="form-select">
                            @foreach ($pengabdian as $p)
                                <option value="{{ $p->id_pengabdian }}" @if ($luaran->id_pengabdian == $p->id_pengabdian) selected @endif>
                                    {{ $p->judul_pengabdian }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori SPMI</label>
                        <select name="id_kategori_spmi" class="form-select">
                            @foreach ($kategoriSpmi as $k)
                                <option value="{{ $k->id_kategori_spmi }}"
                                    @if ($luaran->id_kategori_spmi == $k->id_kategori_spmi) selected @endif>{{ $k->kode_spmi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Luaran</label>
                        <select name="id_jenis_luaran" class="form-select">
                            @foreach ($jenisLuaran as $j)
                                <option value="{{ $j->id_jenis_luaran }}" @if ($luaran->id_jenis_luaran == $j->id_jenis_luaran) selected @endif>
                                    {{ $j->nama_jenis_luaran }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" class="form-control" name="judul" value="{{ $luaran->judul }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tahun</label>
                        <input type="number" class="form-control" name="tahun" min="2000" max="2100"
                            value="{{ $luaran->tahun }}" required>
                    </div>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                    <a href="{{ route('admin.luaran.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
