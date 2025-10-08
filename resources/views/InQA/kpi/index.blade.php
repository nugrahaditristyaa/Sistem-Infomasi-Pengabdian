@extends('inqa.layouts.main')

@section('title', 'Data KPI')

@push('styles')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedheader/3.2.2/css/fixedHeader.bootstrap4.min.css" rel="stylesheet">
    <style>
        .table td,
        .table th {
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fc;
        }

        /* Zebra striping stronger for readability */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #ffffff;
        }

        .table-striped tbody tr:nth-of-type(even) {
            background-color: #fbfcfd;
        }

        .table thead th {
            font-weight: 600;
            color: #4e73df;
            border-bottom-width: 2px;
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

        .no-column {
            width: 56px;
            text-align: center;
            white-space: nowrap;
            padding-left: 6px;
            padding-right: 6px;
        }

        .aksi-column {
            text-align: center;
            width: 120px;
        }

        /* Card padding more roomy */
        .card .card-body {
            padding: 1.5rem;
        }

        .table-hover tbody tr:hover {
            background-color: #f2f2f2 !important;
            cursor: pointer;
        }

        /* Modal styles */
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

        /* Modal Footer Enhancement */
        .modal-footer.bg-light {
            background-color: #f8f9fc !important;
            border-top: 1px solid #e3e6f0;
            padding: 1rem 1.5rem;
        }

        .modal-footer .btn {
            min-width: 100px;
            font-weight: 600;
            border-radius: 0.35rem;
            padding: 0.5rem 1rem;
        }

        .modal-footer .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }

        .modal-footer .btn-secondary:hover {
            background-color: #545b62;
            border-color: #4e555b;
        }

        .modal-footer .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }

        .modal-footer .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }

        .modal-footer .btn-primary:disabled {
            background-color: #6c757d;
            border-color: #6c757d;
            opacity: 0.65;
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data KPI</h1>
        <div class="text-muted">
            <i class="fas fa-info-circle mr-1"></i>
            InQA dapat mengedit KPI yang sudah ada
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tabel KPI</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="dataTable" width="100%"
                            cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="no-column">No</th>
                                    <th>Kode SPMI</th>
                                    <th>Indikator</th>
                                    <th class="text-center">Angka</th>
                                    <th class="text-center">Satuan</th>
                                    <th class="aksi-column">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kpis as $kpi)
                                    <tr>
                                        <td class="no-column">{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="kode-badge">{{ $kpi->kode }}</span>
                                        </td>
                                        <td>{{ $kpi->indikator }}</td>
                                        <td class="text-center">
                                            <span class="target-value">{{ number_format($kpi->target, 0) }}</span>
                                        </td>
                                        <td class="text-center">{{ $kpi->satuan }}</td>
                                        <td class="aksi-column">
                                            <button type="button" class="btn btn-sm btn-warning"
                                                onclick="editKpi('{{ $kpi->kode }}', {{ json_encode($kpi->indikator) }}, '{{ $kpi->target }}', '{{ $kpi->satuan }}')"
                                                title="Edit KPI (Indikator, Angka, Satuan)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
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
                                                <strong>Catatan:</strong> InQA hanya dapat mengedit KPI yang sudah ada,
                                                tidak dapat menambah
                                                atau menghapus KPI.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
                                <i class="fas fa-calculator mr-1 text-primary"></i>Angka (Target) <span
                                    class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" name="target" id="edit_target" min="0"
                                step="1" placeholder="Masukkan nilai angka target" required>
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">Masukkan angka target yang ingin dicapai untuk KPI
                                ini</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Catatan:</strong> Pastikan data yang dimasukkan sesuai dengan standar KPI yang
                            berlaku.
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="saveKpiBtn">
                            <i class="fas fa-save mr-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.2.2/js/dataTables.fixedHeader.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#dataTable').DataTable({
                "processing": true,
                "pageLength": 25,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "Semua"]
                ],
                "order": [
                    [1, "asc"]
                ], // Default sort by Kode KPI
                "columnDefs": [{
                        "orderable": false,
                        "targets": [0, 5] // No dan Aksi columns
                    },
                    {
                        "searchable": false,
                        "targets": [0] // No column
                    }
                ],
                "language": {
                    "processing": "Memproses...",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "zeroRecords": "Tidak ada data KPI yang ditemukan",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "infoFiltered": "(disaring dari _MAX_ total entri)",
                    "search": "Cari:",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                },
                "fixedHeader": true,
                "responsive": true
            });

            // Auto hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
            $('[title]').tooltip();
        });

        // Function to open edit modal
        function editKpi(kode, indikator, target, satuan) {
            // Set form action URL using the route
            var actionUrl = "{{ route('inqa.kpi.updateByCode', ':kode') }}".replace(':kode', kode);

            // Populate form fields
            $('#editKpiForm').attr('action', actionUrl);
            $('#edit_kode').val(kode);
            $('#edit_indikator').val(indikator);
            $('#edit_target').val(parseFloat(target) || 0); // Ensure numeric value
            $('#edit_satuan').val(satuan);

            // Clear previous validation errors
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            // Focus on target field for easy editing
            setTimeout(function() {
                $('#edit_target').focus().select();
            }, 500);

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
            const submitBtn = $('#saveKpiBtn');
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
                    Swal.fire({
                        position: "top",
                        icon: "success",
                        title: "Data KPI berhasil diperbarui!",
                        showConfirmButton: false,
                        timer: 1500
                    });

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

                        Swal.fire({
                            position: "top",
                            icon: "error",
                            title: "Terdapat kesalahan dalam pengisian form!",
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        Swal.fire({
                            position: "top",
                            icon: "error",
                            title: "Terjadi kesalahan saat menyimpan data!",
                            showConfirmButton: false,
                            timer: 2000
                        });
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
