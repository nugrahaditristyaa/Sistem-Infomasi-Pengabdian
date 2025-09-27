@extends('admin.layouts.main')

@section('title', 'Dashboard Staf Fakultas')

@push('styles')
    <style>
        .quick-access-btn .icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .list-group-item-action {
            color: #5a5c69;
        }

        .icon-circle.bg-primary {
            background-color: #4e73df !important;
        }

        .icon-circle.bg-success {
            background-color: #1cc88a !important;
        }

        .icon-circle.bg-info {
            background-color: #36b9cc !important;
        }

        /* Custom styling for statistics cards */
        .statistics-card {
            transition: transform 0.2s ease-in-out;
        }

        .statistics-card:hover {
            transform: translateY(-2px);
        }

        .badge-comparison {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }

        .tooltip-icon {
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .tooltip-icon:hover {
            opacity: 1;
        }

        .clickable-stat {
            transition: color 0.2s;
        }

        .clickable-stat:hover {
            color: #4e73df !important;
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Staf Fakultas</h1>
    </div>

    <div class="card shadow mb-4" id="latestPengabdianCard">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i>Pengabdian Terbaru</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Judul Pengabdian</th>
                            <th>Ketua</th>
                            <th>Tgl Ditambahkan</th>
                            <th>Status Dokumen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestPengabdian as $item)
                            <tr>
                                <td><a
                                        href="{{ route('admin.pengabdian.show', $item->id_pengabdian) }}">{{ Str::limit($item->judul_pengabdian, 40) }}</a>
                                </td>
                                <td>{{ $item->ketua->nama ?? '-' }}</td>
                                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                <td>
                                    {{-- Show overall completeness: Lengkap / Belum Lengkap (button opens modal detail) --}}
                                    @php
                                        $isComplete = $completenessMap[$item->id_pengabdian] ?? false;
                                        // Order must match edit form order and highlight keys
                                        $requiredDocNames = [
                                            'Laporan Akhir',
                                            'Surat Tugas Dosen',
                                            'Surat Permohonan',
                                            'Surat Ucapan Terima Kasih',
                                            'MoU/MoA/Dokumen Kerja Sama Kegiatan',
                                        ];
                                        $jenisByName = [];
                                        foreach ($jenisDokumenList as $jd) {
                                            $jenisByName[$jd->nama_jenis_dokumen] = $jd;
                                        }
                                    @endphp

                                    <button type="button"
                                        class="btn btn-sm {{ $isComplete ? 'btn-success' : 'btn-danger' }}"
                                        data-toggle="modal" data-target="#docsModal{{ $item->id_pengabdian }}">
                                        {{ $isComplete ? 'Lengkap' : 'Belum Lengkap' }}
                                    </button>

                                    <!-- Modal: Detail Dokumen -->
                                    <div class="modal fade" id="docsModal{{ $item->id_pengabdian }}" tabindex="-1"
                                        role="dialog" aria-labelledby="docsModalLabel{{ $item->id_pengabdian }}"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="docsModalLabel{{ $item->id_pengabdian }}">
                                                        Dokumen: {{ Str::limit($item->judul_pengabdian, 120) }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="small text-muted">Ketua: {{ $item->ketua->nama ?? '-' }} —
                                                        Ditambahkan: {{ $item->created_at->format('d/m/Y') }}</p>
                                                    <ul class="list-group">
                                                        @foreach ($requiredDocNames as $name)
                                                            @php
                                                                $jenis = $jenisByName[$name] ?? null;
                                                                $has = false;
                                                                $dok = null;
                                                                if ($jenis) {
                                                                    $dok = $item->dokumen->firstWhere(
                                                                        'id_jenis_dokumen',
                                                                        $jenis->id_jenis_dokumen,
                                                                    );
                                                                    $has = (bool) $dok;
                                                                }
                                                                // Map human label to highlight key used by edit form
                                                                $labelToKeyLocal = [
                                                                    'Laporan Akhir' => 'laporan_akhir',
                                                                    'Surat Tugas Dosen' => 'surat_tugas',
                                                                    'Surat Permohonan' => 'surat_permohonan',
                                                                    'Surat Ucapan Terima Kasih' =>
                                                                        'ucapan_terima_kasih',
                                                                    'MoU/MoA/Dokumen Kerja Sama Kegiatan' =>
                                                                        'kerjasama',
                                                                ];
                                                                $highlightKey = $labelToKeyLocal[$name] ?? null;
                                                            @endphp
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    @if ($has)
                                                                        <i class="fas fa-check-circle text-success mr-2"
                                                                            aria-hidden="true"></i>
                                                                    @else
                                                                        <i class="fas fa-exclamation-triangle text-warning mr-2"
                                                                            aria-hidden="true"></i>
                                                                    @endif
                                                                    <strong>{{ $name }}</strong>
                                                                    @if ($has && $dok->created_at)
                                                                        <div class="small text-muted">
                                                                            {{ $dok->created_at->format('d/m/Y') }}</div>
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    @if ($has)
                                                                        <a href="{{ $dok->url_file }}" target="_blank"
                                                                            class="btn btn-sm btn-outline-secondary">Download</a>
                                                                    @else
                                                                        <a href="{{ route('admin.pengabdian.edit', $item->id_pengabdian) }}{{ $highlightKey ? '?highlight=' . $highlightKey : '' }}#dokumen"
                                                                            target="_blank" rel="noopener noreferrer"
                                                                            class="btn btn-sm btn-outline-success">Unggah</a>
                                                                    @endif
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                        data-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada aktivitas terbaru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 mb-4">
            <div class="row">
                <div class="col-xl-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Rekap Pengabdian per Dosen (Top 5 Ketua)</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-bar" style="height: 250px;"><canvas id="dosenChart"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Distribusi Luaran</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area" style="height: 250px;"><canvas id="luaranTreemapChart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card shadow mb-4 statistics-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-bar mr-2"></i>Statistik Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col-auto">
                            <div class="icon-circle bg-primary">
                                <i class="fas fa-calendar-check text-white"></i>
                            </div>
                        </div>
                        <div class="col ml-3">
                            <div class="d-flex align-items-center">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Pengabdian
                                    <i class="fas fa-info-circle ml-1 tooltip-icon" data-toggle="tooltip"
                                        title="Klik untuk melihat detail pengabdian" style="cursor: pointer;"></i>
                                </div>
                                @if ($stats['percentage_change_pengabdian'] != 0)
                                    <span
                                        class="badge badge-{{ $stats['percentage_change_pengabdian'] > 0 ? 'success' : 'danger' }} ml-2 badge-comparison">
                                        {{ $stats['percentage_change_pengabdian'] > 0 ? '+' : '' }}{{ $stats['percentage_change_pengabdian'] }}%
                                    </span>
                                @endif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 clickable-stat" style="cursor: pointer;"
                                onclick="window.location.href='{{ route('admin.pengabdian.index') }}'">
                                {{ $totalPengabdian }}
                            </div>
                            <div class="text-xs text-muted">
                                Dibandingkan {{ $stats['previous_year'] }}
                            </div>
                        </div>
                    </div>
                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col-auto">
                            <div class="icon-circle bg-success">
                                <i class="fas fa-users text-white"></i>
                            </div>
                        </div>
                        <div class="col ml-3">
                            <div class="d-flex align-items-center">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Dosen Terlibat
                                    <i class="fas fa-info-circle ml-1 tooltip-icon" data-toggle="tooltip"
                                        title="Klik untuk melihat detail dosen" style="cursor: pointer;"></i>
                                </div>
                                @if ($stats['percentage_change_dosen'] != 0)
                                    <span
                                        class="badge badge-{{ $stats['percentage_change_dosen'] > 0 ? 'success' : 'danger' }} ml-2 badge-comparison">
                                        {{ $stats['percentage_change_dosen'] > 0 ? '+' : '' }}{{ $stats['percentage_change_dosen'] }}%
                                    </span>
                                @endif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 clickable-stat" style="cursor: pointer;"
                                onclick="window.location.href='{{ route('admin.dosen.index') }}'">
                                {{ $totalDosenTerlibat }}
                            </div>
                            <div class="text-xs text-muted">
                                Dibandingkan {{ $stats['previous_year'] }}
                            </div>
                        </div>
                    </div>
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto">
                            <div class="icon-circle bg-info">
                                <i class="fas fa-user-graduate text-white"></i>
                            </div>
                        </div>
                        <div class="col ml-3">
                            <div class="d-flex align-items-center">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Pengabdian yang Melibatkan Mahasiswa
                                    <i class="fas fa-info-circle ml-1 tooltip-icon" data-toggle="tooltip"
                                        title="Klik untuk melihat detail mahasiswa" style="cursor: pointer;"></i>
                                </div>
                                @if ($stats['percentage_change_mahasiswa'] != 0)
                                    <span
                                        class="badge badge-{{ $stats['percentage_change_mahasiswa'] > 0 ? 'success' : 'danger' }} ml-2 badge-comparison">
                                        {{ $stats['percentage_change_mahasiswa'] > 0 ? '+' : '' }}{{ $stats['percentage_change_mahasiswa'] }}%
                                    </span>
                                @endif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 clickable-stat" style="cursor: pointer;"
                                onclick="window.location.href='{{ route('admin.mahasiswa.index') }}'">
                                {{ $stats['persentase_pengabdian_dengan_mahasiswa'] }}%
                            </div>
                            <div class="text-xs text-muted">
                                {{ $pengabdianDenganMahasiswa }} dari {{ $totalPengabdian }} pengabdian
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Kelengkapan Dokumen</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2" style="height: 250px;"><canvas id="statusDokumenChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Perlu
                        Tindakan</h6>
                </div>
                <div class="list-group list-group-flush">
                    {{-- Per-required document counts (click to filter modal) --}}
                    @php
                        // Order must match edit form order and highlight keys
                        $requiredDocNames = [
                            'Laporan Akhir',
                            'Surat Tugas Dosen',
                            'Surat Permohonan',
                            'Surat Ucapan Terima Kasih',
                            'MoU/MoA/Dokumen Kerja Sama Kegiatan',
                        ];
                    @endphp
                    @foreach ($requiredDocNames as $rname)
                        <a href="#" data-toggle="modal" data-target="#needActionModal"
                            data-filter="{{ $rname }}"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <div class="font-weight-bold">{{ $rname }}</div>
                                <div class="small text-gray-500">Pengabdian yang belum memiliki dokumen ini</div>
                            </div>
                            <span class="badge badge-danger badge-pill">{{ $missingCounts[$rname] ?? 0 }}</span>
                        </a>
                    @endforeach

                    {{-- 'Belum ada Laporan Akhir' quick link removed per request --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-treemap@2.3.0/dist/chartjs-chart-treemap.min.js"></script>

    <script>
        // Set default font
        Chart.defaults.font.family = 'Nunito',
            '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.color = '#858796';
        // Daftarkan plugin datalabels secara global
        Chart.register(ChartDataLabels);

        // Plugin: draw centered percentage text inside doughnut
        const centerPercentPlugin = {
            id: 'centerPercent',
            beforeDraw: (chart) => {
                if (!chart.config || chart.config.type !== 'doughnut') return;
                const ctx = chart.ctx;
                const {
                    width,
                    height
                } = chart;
                const dataset = chart.data && chart.data.datasets && chart.data.datasets[0];
                if (!dataset) return;
                const data = dataset.data || [];
                const total = data.reduce((a, b) => a + b, 0);
                const complete = data[0] || 0;
                const percent = total ? Math.round((complete * 100) / total) : 0;

                ctx.save();
                // Determine font sizes responsive to canvas size
                const fontSize = Math.round(Math.min(width, height) / 6);
                ctx.font = `bold ${fontSize}px Nunito, Arial, sans-serif`;
                ctx.fillStyle = '#4e73df';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                // Primary percentage
                ctx.fillText(percent + '%', width / 2, height / 2 - (fontSize * 0.08));
                // Small label under the number
                ctx.font = `${Math.max(Math.round(fontSize / 3), 12)}px Nunito, Arial, sans-serif`;
                ctx.fillStyle = '#858796';
                ctx.fillText('Lengkap', width / 2, height / 2 + Math.round(fontSize / 2.2));
                ctx.restore();
            }
        };
        Chart.register(centerPercentPlugin);

        // 1. Grafik Status Kelengkapan Dokumen (Doughnut Chart dengan Persentase)
        new Chart(document.getElementById("statusDokumenChart"), {
            type: 'doughnut',
            data: {
                labels: ["Dokumen Lengkap", "Belum Lengkap"],
                datasets: [{
                    data: [{{ $pengabdianLengkap }}, {{ $tanpaLaporanAkhir }}],
                    backgroundColor: ['#1cc88a', '#f6c23e'],
                    hoverBackgroundColor: ['#17a673', '#dda20a'],
                    borderColor: '#fff',
                    borderWidth: 2
                }],
            },
            options: {
                maintainAspectRatio: false,
                cutout: '80%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        enabled: false
                    },
                    datalabels: {
                        color: '#fff',
                        font: {
                            weight: 'bold'
                        },
                        formatter: (value, ctx) => {
                            let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            if (value === 0 || sum === 0) return '';
                            let percentage = (value * 100 / sum).toFixed(0) + "%";
                            return percentage;
                        }
                    }
                }
            }
        });

        // 2. Grafik Rekap Pengabdian per Dosen (Horizontal Bar Chart)
        new Chart(document.getElementById("dosenChart"), {
            type: 'bar',
            data: {
                labels: @json($namaDosen),
                datasets: [{
                    label: "Jumlah Pengabdian",
                    data: @json($jumlahPengabdianDosen),
                    backgroundColor: '#4e73df',
                }],
            },
            options: {
                indexAxis: 'y',
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // 3. Grafik Distribusi Luaran (Treemap)
        new Chart(document.getElementById("luaranTreemapChart"), {
            type: 'treemap',
            data: {
                datasets: [{
                    label: 'Jumlah Luaran',
                    tree: @json($dataTreemap),
                    key: 'v',
                    groups: ['g'],
                    backgroundColor: (ctx) => {
                        const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                            '#858796'
                        ];
                        return ctx.type === 'data' ? colors[ctx.dataIndex % colors.length] :
                            'transparent';
                    },
                    labels: {
                        display: true,
                        color: 'white',
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        formatter: (ctx) => ctx.type === 'data' ? [ctx.raw.g, ctx.raw.v] : null
                    },
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.raw.g}: ${ctx.raw.v}`
                        }
                    }
                }
            }
        });
    </script>
    {{-- Modal: Daftar Pengabdian Perlu Tindakan --}}
    <div class="modal fade" id="needActionModal" tabindex="-1" role="dialog" aria-labelledby="needActionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="needActionModalLabel">Pengabdian yang Perlu Tindakan
                        ({{ $needActionCount ?? 0 }})</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    @if (!empty($pengabdianNeedingDocs) && count($pengabdianNeedingDocs) > 0)
                        <div class="list-group">
                            @foreach ($pengabdianNeedingDocs as $p)
                                @php
                                    // normalize missing labels: accept either human label or internal key
                                    $labelToKey = [
                                        'Laporan Akhir' => 'laporan_akhir',
                                        'Surat Tugas Dosen' => 'surat_tugas',
                                        'Surat Permohonan' => 'surat_permohonan',
                                        'Surat Ucapan Terima Kasih' => 'ucapan_terima_kasih',
                                        'MoU/MoA/Dokumen Kerja Sama Kegiatan' => 'kerjasama',
                                    ];
                                    // preferred order (same as edit form)
                                    $preferred = array_keys($labelToKey);

                                    $rawMissing = $p['missing'] ?? [];
                                    $missingLabels = [];
                                    foreach ($rawMissing as $m) {
                                        if (isset($labelToKey[$m])) {
                                            // already a human label
                                            $missingLabels[] = $m;
                                        } else {
                                            // maybe it's an internal key -> find corresponding label
        $labelFound = array_search($m, $labelToKey, true);
        if ($labelFound !== false) {
            $missingLabels[] = $labelFound;
        } else {
            // unknown value, keep as-is
            $missingLabels[] = $m;
        }
    }
}
// build data-missing to include both labels and keys for robust client-side matching
$missingKeys = array_map(fn($lab) => $labelToKey[$lab] ?? $lab, $missingLabels);
$dataMissingArr = array_values(
    array_unique(array_merge($missingLabels, $missingKeys)),
);
$dataMissing = implode('|', $dataMissingArr);

                                    // determine first missing by preferred order
                                    $firstMissing = null;
                                    foreach ($preferred as $lab) {
                                        if (in_array($lab, $missingLabels, true)) {
                                            $firstMissing = $lab;
                                            break;
                                        }
                                    }
                                    if (!$firstMissing) {
                                        $firstMissing = $missingLabels[0] ?? null;
                                    }
                                    $highlight = $firstMissing ? $labelToKey[$firstMissing] ?? null : null;
                                @endphp
                                <div class="list-group-item" data-missing="{{ $dataMissing }}">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><a
                                                    href="{{ route('admin.pengabdian.show', $p['id']) }}">{{ Str::limit($p['judul'], 80) }}</a>
                                            </h6>
                                            <small class="text-muted">Ketua: {{ $p['ketua'] }}</small>
                                        </div>
                                        <div class="text-right">
                                            <span class="badge badge-danger mr-2">{{ count($p['missing']) }} kurang</span>
                                            <a href="{{ route('admin.pengabdian.edit', $p['id']) }}{{ $highlight ? '?highlight=' . $highlight : '' }}#dokumen"
                                                target="_blank" rel="noopener noreferrer"
                                                class="btn btn-sm btn-primary">Lengkapi Dokumen</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-muted">Tidak ada pengabdian yang perlu tindakan.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        // make modal/list initialization callable so we can re-run after dynamic updates
        window.initNeedActionModal = function() {
            // store original list items markup to filter client-side (use data-missing)
            var originalItems = [];
            $('#needActionModal .list-group .list-group-item').each(function() {
                originalItems.push({
                    html: $(this).prop('outerHTML'),
                    missing: $(this).attr('data-missing') || ''
                });
            });

            // serialize missingCounts from server for client-side lookups
            var missingCounts = @json($missingCounts ?? []);

            // unbind first to avoid duplicate handlers
            $('.list-group-item-action[data-filter]').off('click.initNeedAction');
            $('.list-group-item-action[data-filter]').on('click.initNeedAction', function(e) {
                var filter = $(this).data('filter');
                // update modal title using client-side map
                var cnt = (missingCounts && missingCounts[filter]) ? missingCounts[filter] : 0;
                $('#needActionModalLabel').text('Pengabdian yang perlu dokumen: ' + filter + ' (' + cnt + ')');
                // rebuild modal list with only items whose missing list contains the filter
                var container = $('#needActionModal .modal-body .list-group');
                if (!container.length) {
                    // no items present (server-side empty), just show modal
                    return;
                }
                container.empty();
                var originalHtml = '';

                // client-side map label -> key (mirror server mapping)
                var labelToKeyClient = {
                    'Laporan Akhir': 'laporan_akhir',
                    'Surat Tugas Dosen': 'surat_tugas',
                    'Surat Permohonan': 'surat_permohonan',
                    'Surat Ucapan Terima Kasih': 'ucapan_terima_kasih',
                    'MoU/MoA/Dokumen Kerja Sama Kegiatan': 'kerjasama'
                };
                var desiredKey = labelToKeyClient[filter] || filter;

                originalItems.forEach(function(it) {
                    if (it.missing.indexOf(filter) !== -1) {
                        // build jquery element so we can modify the link safely
                        var $el = $(it.html);
                        // find the primary action button (Lengkapi Dokumen) and rewrite href
                        $el.find('a.btn-primary').each(function() {
                            try {
                                var $a = $(this);
                                var href = $a.attr('href') || '';
                                // remove any existing highlight param
                                href = href.replace(/([?&])highlight=[^&]*(&?)/, function(_, p1,
                                    p2) {
                                    return p2 ? p1 : '';
                                });
                                var sep = href.indexOf('?') === -1 ? '?' : '&';
                                // ensure anchor points to #dokumen
                                href = href.split('#')[0];
                                href = href + sep + 'highlight=' + encodeURIComponent(
                                    desiredKey) + '#dokumen';
                                $a.attr('href', href);
                            } catch (e) {
                                console.warn('Failed to rewrite Lengkapi Dokumen href', e);
                            }
                        });
                        container.append($el);
                    }
                    originalHtml += it.html;
                });
                // save originalHtml in container data attribute for restore
                container.data('originalHtml', originalHtml);
            });

            // when modal is hidden, restore original title and content
            $('#needActionModal').off('hidden.initNeedAction').on('hidden.initNeedAction', function() {
                $('#needActionModalLabel').text(
                    'Pengabdian yang Perlu Tindakan ({{ $needActionCount ?? 0 }})');
                var container = $('#needActionModal .modal-body .list-group');
                if (container.length) {
                    var originalHtml = container.data('originalHtml') || originalItems.map(function(it) {
                        return it.html;
                    }).join('');
                    container.html(originalHtml);
                }
            });
        };

        // initial run
        window.initNeedActionModal();

        // Initialize tooltips with configuration
        $(function() {
            $('[data-toggle="tooltip"]').tooltip({
                placement: 'top',
                trigger: 'hover focus',
                delay: {
                    "show": 500,
                    "hide": 100
                },
                html: true
            });
        });

        // Polling: refresh latest pengabdian card every 15 seconds
        (function() {
            var pollingInterval = 15000; // 15s
            var timer = null;

            function refreshLatestCard() {
                try {
                    fetch(window.location.href, {
                        credentials: 'same-origin'
                    }).then(function(resp) {
                        return resp.text();
                    }).then(function(htmlText) {
                        try {
                            var parser = new DOMParser();
                            var doc = parser.parseFromString(htmlText, 'text/html');
                            var newCard = doc.querySelector('#latestPengabdianCard');
                            var oldCard = document.querySelector('#latestPengabdianCard');
                            if (newCard && oldCard) {
                                oldCard.innerHTML = newCard.innerHTML;
                                // re-run modal init to rebind handlers
                                if (window.initNeedActionModal) window.initNeedActionModal();
                            }
                        } catch (e) {
                            console.warn('Failed to parse refreshed dashboard HTML', e);
                        }
                    }).catch(function(err) {
                        console.warn('Failed to fetch dashboard for refresh', err);
                    });
                } catch (e) {
                    console.warn('refreshLatestCard error', e);
                }
            }

            // start polling
            timer = setInterval(refreshLatestCard, pollingInterval);
            // Also do an immediate refresh once after load to pick up very recent changes
            setTimeout(refreshLatestCard, 2000);
        })();
    </script>
@endpush
