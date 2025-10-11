<script>
            // Year Filter Handler
            document.addEventListener('DOMContentLoaded', function() {
                const yearFilter = document.getElementById('yearFilter');
                if (yearFilter) {
                    yearFilter.addEventListener('change', function() {
                        const selectedYear = this.value;
                        const currentUrl = new URL(window.location);

                        if (selectedYear === 'all') {
                            currentUrl.searchParams.delete('year');
                        } else {
                            currentUrl.searchParams.set('year', selectedYear);
                        }

                        // Show loading indicator
                        const loadingHtml =
                            '<div class="d-flex justify-content-center align-items-center" style="height: 200px;"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>';

                        // Add loading to chart containers
                        const chartContainers = [
                            document.querySelector('#dosenChart')?.parentElement,
                            document.querySelector('#luaranBarChart')?.parentElement,
                            document.querySelector('#statusDokumenChart')?.parentElement
                        ];

                        chartContainers.forEach(container => {
                            if (container) {
                                container.innerHTML = loadingHtml;
                            }
                        });

                        // Redirect to update dashboard
                        window.location.href = currentUrl.toString();
                    });
                }
            });

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
                        data: [{{ $pengabdianLengkap }}, {{ $pengabdianTidakLengkap }}],
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
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#fff',
                            borderWidth: 1,
                            cornerRadius: 6,
                            displayColors: true,
                            callbacks: {
                                title: function(context) {
                                    return 'Status Kelengkapan Dokumen';
                                },
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;

                                    return [
                                        `${label}: ${value} pengabdian`,
                                        `Persentase: ${percentage}%`,
                                        `Total keseluruhan: ${total} pengabdian`
                                    ];
                                },
                                footer: function(context) {
                                    const totalComplete = {{ $pengabdianLengkap }};
                                    const totalIncomplete = {{ $pengabdianTidakLengkap }};
                                    const total = totalComplete + totalIncomplete;

                                    if (total > 0) {
                                        const completionRate = ((totalComplete / total) * 100).toFixed(1);
                                        return `Tingkat kelengkapan: ${completionRate}%`;
                                    }
                                    return '';
                                }
                            }
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

            // 2. Grafik Rekap Pengabdian per Dosen (Horizontal Bar Chart dengan angka di kanan)
            let dosenChart;
            let originalDosenData = {
                labels: @json($namaDosen),
                data: @json($jumlahPengabdianDosen)
            };

            // Function untuk membuat atau update chart dosen
            function createDosenChart(sortOrder = 'desc') {
                // Kombinasi data untuk sorting
                let combinedData = originalDosenData.labels.map((label, index) => ({
                    name: label,
                    value: originalDosenData.data[index]
                }));

                // Sort data
                combinedData.sort((a, b) => {
                    return sortOrder === 'asc' ? a.value - b.value : b.value - a.value;
                });

                // Batasi hanya 5 dosen terbanyak
                const maxDisplay = 5;
                combinedData = combinedData.slice(0, maxDisplay);

                // Pisahkan kembali setelah sort dan limit
                let sortedLabels = combinedData.map(item => item.name);
                let sortedData = combinedData.map(item => item.value);

                // Destroy chart lama jika ada
                if (dosenChart) {
                    dosenChart.destroy();
                }

                dosenChart = new Chart(document.getElementById("dosenChart"), {
                    type: 'bar',
                    data: {
                        labels: sortedLabels,
                        datasets: [{
                            label: "Jumlah Pengabdian",
                            data: sortedData,
                            backgroundColor: '#4e73df',
                            borderRadius: 4,
                            borderSkipped: false,
                        }],
                    },
                    options: {
                        indexAxis: 'y',
                        maintainAspectRatio: false,
                        responsive: true,
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            y: {
                                ticks: {
                                    font: {
                                        size: 14
                                    },
                                    maxTicksLimit: 5
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.parsed.x} pengabdian`;
                                    }
                                }
                            },
                            datalabels: {
                                display: true,
                                color: '#2d3e50',
                                anchor: 'end',
                                align: 'right',
                                offset: 8,
                                font: {
                                    weight: 'bold',
                                    size: 14
                                },
                                formatter: function(value) {
                                    return value;
                                }
                            }
                        },
                        layout: {
                            padding: {
                                right: 50, // Padding lebih besar untuk angka di kanan
                                top: 10,
                                bottom: 10,
                                left: 10
                            }
                        }
                    }
                });
            }

            // Buat chart pertama kali (default descending)
            createDosenChart('desc');

            // 2b. Grafik Rekap Lengkap Pengabdian per Dosen (All Data with Scroll)
            let dosenAllChart;
            let originalAllDosenData = {
                labels: @json($namaDosen),
                data: @json($jumlahPengabdianDosen)
            };

            // Function untuk membuat chart semua dosen
            function createDosenAllChart(sortOrder = 'desc') {
                // Kombinasi data untuk sorting
                let combinedAllData = originalAllDosenData.labels.map((label, index) => ({
                    name: label,
                    value: originalAllDosenData.data[index]
                }));

                // Sort data
                combinedAllData.sort((a, b) => {
                    return sortOrder === 'asc' ? a.value - b.value : b.value - a.value;
                });

                // Pisahkan kembali setelah sort (tampilkan semua data)
                let sortedAllLabels = combinedAllData.map(item => item.name);
                let sortedAllData = combinedAllData.map(item => item.value);

                // Destroy chart lama jika ada
                if (dosenAllChart) {
                    dosenAllChart.destroy();
                }

                dosenAllChart = new Chart(document.getElementById("dosenAllChart"), {
                    type: 'bar',
                    data: {
                        labels: sortedAllLabels,
                        datasets: [{
                            label: "Jumlah Pengabdian",
                            data: sortedAllData,
                            backgroundColor: '#4e73df',
                            borderRadius: 6,
                            borderSkipped: false,
                        }],
                    },
                    options: {
                        indexAxis: 'y',
                        maintainAspectRatio: false,
                        responsive: true,
                        interaction: {
                            intersect: false,
                            mode: 'nearest'
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: 12
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            },
                            y: {
                                ticks: {
                                    font: {
                                        size: 12
                                    },
                                    maxRotation: 0,
                                    minRotation: 0
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: '#4e73df',
                                borderWidth: 1,
                                cornerRadius: 6,
                                displayColors: true,
                                callbacks: {
                                    title: function(context) {
                                        return context[0].label;
                                    },
                                    label: function(context) {
                                        return `Jumlah Pengabdian: ${context.parsed.x}`;
                                    },
                                    afterLabel: function(context) {
                                        const totalPengabdian = sortedAllData.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed.x / totalPengabdian) * 100).toFixed(1);
                                        return `Persentase: ${percentage}%`;
                                    }
                                }
                            },
                            datalabels: {
                                display: true,
                                color: '#2d3e50',
                                anchor: 'end',
                                align: 'right',
                                offset: 8,
                                font: {
                                    weight: 'bold',
                                    size: 11
                                },
                                formatter: function(value) {
                                    return value > 0 ? value : '';
                                }
                            }
                        },
                        layout: {
                            padding: {
                                right: 60, // Extra padding untuk data labels
                                top: 15,
                                bottom: 15,
                                left: 15
                            }
                        }
                    }
                });
            }

            // Buat chart semua dosen pertama kali
            createDosenAllChart('desc');

            // Toggle functionality untuk switch antara Top 5 dan All Data
            const viewTop5Btn = document.getElementById('viewTop5Btn');
            const viewAllBtn = document.getElementById('viewAllBtn');
            const top5Container = document.getElementById('top5ChartContainer');
            const allContainer = document.getElementById('allChartContainer');
            const currentViewBadge = document.getElementById('currentViewBadge');
            const chartInfoText = document.getElementById('chartInfoText');

            // Function to switch to Top 5 view
            function switchToTop5() {
                // Update buttons
                viewTop5Btn.classList.remove('btn-outline-primary');
                viewTop5Btn.classList.add('btn-primary', 'active');
                viewAllBtn.classList.remove('btn-primary', 'active');
                viewAllBtn.classList.add('btn-outline-primary');

                // Update badge and info text
                currentViewBadge.textContent = 'Top 5';
                currentViewBadge.className = 'badge badge-info ml-2';
                chartInfoText.innerHTML =
                    '<i class="fas fa-info-circle mr-1"></i>Menampilkan 5 dosen dengan pengabdian terbanyak';

                // Switch containers
                allContainer.classList.add('d-none');
                top5Container.classList.remove('d-none');
            }

            // Function to switch to All Data view
            function switchToAll() {
                // Update buttons
                viewAllBtn.classList.remove('btn-outline-primary');
                viewAllBtn.classList.add('btn-primary', 'active');
                viewTop5Btn.classList.remove('btn-primary', 'active');
                viewTop5Btn.classList.add('btn-outline-primary');

                // Update badge and info text
                currentViewBadge.textContent = 'Semua Data';
                currentViewBadge.className = 'badge badge-success ml-2';
                const totalDosen = @json(count($namaDosen ?? []));
                chartInfoText.innerHTML =
                    `<i class="fas fa-scroll mr-1"></i>Menampilkan semua dosen dengan scroll (Total: ${totalDosen} dosen)`;

                // Switch containers
                top5Container.classList.add('d-none');
                allContainer.classList.remove('d-none');
            }

            // Event listeners for toggle buttons
            if (viewTop5Btn && viewAllBtn) {
                viewTop5Btn.addEventListener('click', function() {
                    switchToTop5();
                });

                viewAllBtn.addEventListener('click', function() {
                    switchToAll();
                });
            }

            // Event handler untuk tombol sort (dengan error handling)
            const dosenSortBtn = document.getElementById('dosenSortBtn');
            if (dosenSortBtn) {
                dosenSortBtn.addEventListener('click', function() {
                    const currentOrder = this.getAttribute('data-order');
                    const newOrder = currentOrder === 'desc' ? 'asc' : 'desc';

                    // Update button
                    this.setAttribute('data-order', newOrder);
                    const icon = this.querySelector('i');

                    if (newOrder === 'asc') {
                        icon.className = 'fas fa-sort-amount-up';
                        this.setAttribute('title', 'Urutkan jumlah (terendah)');
                    } else {
                        icon.className = 'fas fa-sort-amount-down';
                        this.setAttribute('title', 'Urutkan jumlah (tertinggi)');
                    }

                    // Update chart berdasarkan view yang aktif
                    if (!top5Container.classList.contains('d-none')) {
                        createDosenChart(newOrder);
                    } else {
                        createDosenAllChart(newOrder);
                    }
                });
            }

            // Plugin treemap sudah tidak diperlukan - menggunakan bar chart

            // 3. Grafik Distribusi Luaran (Bar Chart)
            @if (count($dataTreemap) > 0)
                // Data untuk bar chart distribusi luaran
                const luaranData = @json($dataTreemap);

                // Sortir data berdasarkan jumlah luaran (descending)
                luaranData.sort((a, b) => b.v - a.v);

                const labels = luaranData.map(item => item.g);
                const values = luaranData.map(item => item.v);
                const colors = [
                    '#4e73df', '#1cc88a', '#36b9cc',
                    '#f6c23e', '#e74a3b', '#858796',
                    '#6f42c1', '#fd7e14', '#20c997'
                ];

                new Chart(document.getElementById("luaranBarChart"), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Luaran',
                            data: values,
                            backgroundColor: colors.slice(0, labels.length),
                            borderColor: colors.slice(0, labels.length),
                            borderWidth: 1,
                            borderRadius: 4,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        interaction: {
                            intersect: false,
                            mode: 'nearest'
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    maxRotation: 45,
                                    minRotation: 0,
                                    callback: function(value, index, ticks) {
                                        const label = this.getLabelForValue(value);
                                        return label.length > 15 ? label.substring(0, 12) + '...' : label;
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                },
                                ticks: {
                                    stepSize: 1,
                                    precision: 0,
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true,
                                mode: 'nearest',
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                cornerRadius: 6,
                                displayColors: true,
                                callbacks: {
                                    title: function(context) {
                                        return context[0].label;
                                    },
                                    label: function(context) {
                                        const value = context.parsed.y; // Untuk vertical bar, nilai ada di y
                                        const total = values.reduce((sum, val) => sum + val, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;

                                        return [
                                            `Jumlah: ${value} luaran`,
                                            `Persentase: ${percentage}%`,
                                            `Ranking: #${context.dataIndex + 1}`
                                        ];
                                    },
                                    labelColor: function(context) {
                                        return {
                                            borderColor: context.dataset.backgroundColor[context.dataIndex],
                                            backgroundColor: context.dataset.backgroundColor[context.dataIndex],
                                            borderWidth: 2,
                                            borderRadius: 4
                                        };
                                    }
                                }
                            }
                        },
                        layout: {
                            padding: {
                                top: 10,
                                right: 20,
                                bottom: 10,
                                left: 10
                            }
                        }
                    }
                });
            @endif
        </script>