{{-- BACKUP TEMPLATE - This entire file is intentionally ignored by Blade compiler to avoid syntax checks. --}}
{{--
Bagian yang bermasalah di dalam <tbody>

@if ($kpis->count() > 0)
    <!-- Ganti struktur yang lama dengan @forelse -->
    @forelse ($kpis as $index => $kpi)
        <tr>
            <td class="text-center">
                <span class="text-muted">{{ $index + 1 }}</span>
            </td>
            <td>
                <span class="kode-badge">{{ $kpi->kode }}</span>
            </td>
            <td>
                <div class="text-dark">{{ $kpi->indikator }}</div>
            </td>
            <td class="text-center">
                <span class="target-value">{{ number_format($kpi->target, 0) }}</span>
            </td>
            <td class="text-center">
                <span class="text-muted">{{ $kpi->satuan }}</span>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-warning btn-action"
                    onclick="editKpi('{{ $kpi->kode }}', {{ json_encode($kpi->indikator) }}, '{{ $kpi->target }}', '{{ $kpi->satuan }}')"
                    title="Edit KPI">
                    <i class="fas fa-edit mr-1"></i>Edit
                </button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-4">
                <i class="fas fa-info-circle mr-2"></i>Tidak ada data KPI yang tersedia
            </td>
        </tr>
    @endforelse
@else
    <!-- Konten di dalam
@else
dan @endif (untuk kasus data kosong) -->
--}}
<div class="text-center py-5">
    <div class="mb-4">
        <i class="fas fa-chart-bar fa-4x text-gray-300"></i>
    </div>
    <h5 class="text-gray-600 mb-3">Belum Ada Data KPI</h5>
    <p class="text-gray-500 mb-4">
        Data KPI (Key Performance Indicator) belum tersedia.<br>
        Hubungi administrator untuk menambahkan data KPI.
    </p>
    <div class="alert alert-info">
        <i class="fas fa-info-circle mr-2"></i>
        <strong>Catatan:</strong> InQA hanya dapat mengedit KPI yang sudah ada, tidak dapat menambah
        atau menghapus KPI.
    </div>
</div>
@endif
**Perhatikan perubahan:** Struktur perulangan di dalam `<tbody>` yang saya berikan di atas sudah menggunakan `@forelse`
    yang benar, menggantikan kombinasi `@foreach` dan `@empty` yang salah dalam cuplikan kode Anda.

Namun, karena cuplikan kode Blade yang Anda berikan terpotong dan tidak lengkap (memiliki `@else` dan `@endif` di luar `
<tbody>`), ini adalah versi lengkap yang memperbaiki semua masalah sintaks:

    ```html
    @extends('dekan.layouts.main')

    @section('title', 'Data KPI - InQA Dashboard')

    @push('styles')
        <style>
            .table-hover tbody tr:hover {
                background-color: #f8f9fc;
            }

            .btn-action {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
                margin: 2px;
            }

            .table th {
                background-color: #f8f9fc;
                border-top: 1px solid #e3e6f0;
                font-weight: 600;
                text-transform: uppercase;
                font-size: 0.75rem;
                color: #5a5c69;
            }

            .table td {
                vertical-align: middle;
                padding: 0.75rem;
            }

            .kode-badge {
                background-color: #4e73df;
                color: white;
                padding: 0.25rem 0.5rem;
                border-radius: 0.25rem;
                font-size: 0.75rem;
                font-weight: 600;
            }

            .target-value {
                font-weight: 600;
                color: #1cc88a;
            }

            .modal-header.bg-primary {
                background-color: #4e73df !important;
            }

            .modal-header .close {
                color: white;
                opacity: 0.8;
            }

            .modal-header .close:hover {
                opacity: 1;
            }

            .btn-action:hover {
                transform: translateY(-1px);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .form-group label {
                color: #5a5c69;
                margin-bottom: 0.5rem;
            }

            .text-danger {
                color: #e74a3b !important;
            }

            .invalid-feedback {
                display: block;
            }
        </style>
    @endpush

    @section('content')
        <div class="container-fluid">
            <!-- Header -->
            <div class="mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-chart-bar mr-2 text-primary"></i>Data KPI
                </h1>
                <small class="text-muted">Kelola Key Performance Indicator (KPI) untuk monitoring kinerja</small>
            </div>

            <!-- Alert Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif


            <!-- Main Content Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list mr-2"></i>Daftar KPI
                    </h6>

                    <span class="badge badge-primary badge-pill">
                        {{ $kpis->count() }} Data
                    </span>
                </div>

                <div class="card-body">
                    @if ($kpis->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="8%" class="text-center">No</th>
                                        <th width="15%">Kode</th>
                                        <th width="45%">Indikator</th>
                                        <th width="10%" class="text-center">Angka</th>
                                        <th width="10%" class="text-center">Satuan</th>
                                        <th width="12%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kpis as $index => $kpi)
                                        <tr>
                                            <td class="text-center">
                                                <span class="text-muted">{{ $index + 1 }}</span>
                                            </td>
                                            <td>
                                                <span class="kode-badge">{{ $kpi->kode }}</span>
                                            </td>
                                            <td>
                                                <div class="text-dark">{{ $kpi->indikator }}</div>
                                            </td>
                                            <td class="text-center">
                                                <span class="target-value">{{ number_format($kpi->target, 0) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="text-muted">{{ $kpi->satuan }}</span>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-warning btn-action"
                                                    onclick="editKpi('{{ $kpi->kode }}', {{ json_encode($kpi->indikator) }}, '{{ $kpi->target }}', '{{ $kpi->satuan }}')"
                                                    title="Edit KPI">
                                                    <i class="fas fa-edit mr-1"></i>Edit
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-chart-bar fa-4x text-gray-300"></i>
                            </div>
                            <h5 class="text-gray-600 mb-3">Belum Ada Data KPI</h5>
                            <p class="text-gray-500 mb-4">
                                Data KPI (Key Performance Indicator) belum tersedia.<br>
                                Hubungi administrator untuk menambahkan data KPI.
                            </p>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Catatan:</strong> InQA hanya dapat mengedit KPI yang sudah ada, tidak dapat menambah
                                atau menghapus KPI.
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            <!-- Modal Edit KPI -->
            <div class="modal fade" id="editKpiModal" tabindex="-1" role="dialog" aria-labelledby="editKpiModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="editKpiModalLabel">
                                <i class="fas fa-edit mr-2"></i>Edit KPI
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="editKpiForm" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label font-weight-bold">
                                                <i class="fas fa-code mr-1 text-primary"></i>Kode KPI
                                            </label>
                                            <input type="text" class="form-control" id="edit_kode" disabled>
                                            <small class="form-text text-muted">Kode KPI tidak dapat diubah</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label font-weight-bold">
                                                <i class="fas fa-tag mr-1 text-primary"></i>Satuan <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" name="satuan" id="edit_satuan"
                                                placeholder="Contoh: %, buah, orang" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label font-weight-bold">
                                        <i class="fas fa-bullseye mr-1 text-primary"></i>Nama Indikator <span
                                            class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" name="indikator" id="edit_indikator" rows="4"
                                        placeholder="Masukkan nama indikator KPI" required></textarea>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label font-weight-bold">
                                        <i class="fas fa-target mr-1 text-primary"></i>Target <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" name="target" id="edit_target"
                                        min="0" step="0.01" placeholder="Masukkan nilai target" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Catatan:</strong> Pastikan data yang dimasukkan sesuai dengan standar KPI
                                    yang
                                    berlaku.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times mr-1"></i>Batal
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i>Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize DataTable
                $('#dataTable').DataTable({
                    "pageLength": 10,
                    "order": [
                        [1, "asc"]
                    ],
                    "columnDefs": [{
                            "orderable": false,
                            "targets": [0, 5]
                        }, // Disable sorting for No and Actions columns
                        {
                            "searchable": false,
                            "targets": [0, 5]
                        } // Disable search for No and Actions columns
                    ],
                    "language": {
                        "search": "Cari:",
                        "lengthMenu": "Tampilkan _MENU_ data per halaman",
                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        "infoEmpty": "Tidak ada data yang ditampilkan",
                        "paginate": {
                            "next": "Selanjutnya",
                            "previous": "Sebelumnya"
                        },
                        "emptyTable": "Tidak ada data KPI yang tersedia"
                    }
                });

                // Auto hide alerts
                setTimeout(function() {
                    $('.alert').fadeOut('slow');
                }, 5000);

                // Initialize tooltips
                $('[title]').tooltip();
            });

            // Function to open edit modal
            function editKpi(kode, indikator, target, satuan) {
                // Set form action URL using the route
                var actionUrl = "{{ route('dekan.kpi.updateByCode', ':kode') }}".replace(':kode', kode);

                // Populate form fields
                $('#editKpiForm').attr('action', actionUrl);
                $('#edit_kode').val(kode);
                $('#edit_indikator').val(indikator);
                $('#edit_target').val(target);
                $('#edit_satuan').val(satuan);

                // Clear previous validation errors
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                // Show modal
                $('#editKpiModal').modal('show');
            }

            // Handle form submission
            $('#editKpiForm').on('submit', function(e) {
                e.preventDefault();

                // Clear previous validation errors
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                // Get form data
                const formData = new FormData(this);
                const actionUrl = $(this).attr('action');

                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...').prop('disabled', true);

                // Submit via AJAX
                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Show success message
                        toastr.success('Data KPI berhasil diperbarui!');

                        // Close modal
                        $('#editKpiModal').modal('hide');

                        // Reload page or update table data
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;

                            // Display validation errors
                            Object.keys(errors).forEach(function(field) {
                                $('[name="' + field + '"]').addClass('is-invalid');
                                $('[name="' + field + '"]').siblings('.invalid-feedback').text(
                                    errors[field][0]);
                            });

                            toastr.error('Terdapat kesalahan dalam pengisian form!');
                        } else {
                            toastr.error('Terjadi kesalahan saat menyimpan data!');
                        }
                    },
                    complete: function() {
                        // Reset button state
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Reset form when modal is closed
            $('#editKpiModal').on('hidden.bs.modal', function() {
                $('#editKpiForm')[0].reset();
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            });
        </script>
    @endpush
