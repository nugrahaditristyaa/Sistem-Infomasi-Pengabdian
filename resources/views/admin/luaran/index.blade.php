@extends('admin.layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Luaran</h4>
            <a href="{{ route('admin.luaran.create') }}" class="btn btn-primary">Tambah</a>
        </div>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pengabdian</th>
                            <th>Kategori SPMI</th>
                            <th>Jenis Luaran</th>
                            <th>Judul</th>
                            <th>Tahun</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($luaran as $row)
                            <tr>
                                <td>{{ $row->id_luaran }}</td>
                                <td>{{ $row->pengabdian->judul_pengabdian ?? '-' }}</td>
                                <td>{{ $row->kategoriSpmi->kode_spmi ?? '-' }}</td>
                                <td>{{ $row->jenisLuaran->nama_jenis_luaran ?? '-' }}</td>
                                <td>{{ $row->judul }}</td>
                                <td>{{ $row->tahun }}</td>
                                <td>
                                    <a href="{{ route('admin.luaran.show', $row->id_luaran) }}"
                                        class="btn btn-sm btn-info">Detail</a>
                                    <a href="{{ route('admin.luaran.edit', $row->id_luaran) }}"
                                        class="btn btn-sm btn-warning">Ubah</a>
                                    <form action="{{ route('admin.luaran.destroy', $row->id_luaran) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Hapus data?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $luaran->links() }}
            </div>
        </div>
    </div>
@endsection
