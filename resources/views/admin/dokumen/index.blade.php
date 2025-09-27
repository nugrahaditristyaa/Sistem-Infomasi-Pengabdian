@extends('admin.layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Dokumen</h4>
            <a href="{{ route('admin.dokumen.create') }}" class="btn btn-primary">Tambah</a>
        </div>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pengabdian</th>
                            <th>Jenis</th>
                            <th>Nama File</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dokumen as $row)
                            <tr>
                                <td>{{ $row->id_dokumen }}</td>
                                <td>{{ $row->pengabdian->judul_pengabdian ?? '-' }}</td>
                                <td>{{ $row->jenisDokumen->nama_jenis_dokumen ?? '-' }}</td>
                                <td>{{ $row->nama_file }}</td>
                                <td>
                                    <a href="{{ route('admin.dokumen.show', $row->id_dokumen) }}"
                                        class="btn btn-sm btn-info">Detail</a>
                                    <a href="{{ route('admin.dokumen.edit', $row->id_dokumen) }}"
                                        class="btn btn-sm btn-warning">Ubah</a>
                                    <form action="{{ route('admin.dokumen.destroy', $row->id_dokumen) }}" method="POST"
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
                {{ $dokumen->links() }}
            </div>
        </div>
    </div>
@endsection
