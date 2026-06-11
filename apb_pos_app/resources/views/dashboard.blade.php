@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        .dashboard-page {
            margin-top: -3rem;
            position: relative;
            z-index: 1;
        }

        .dashboard-title-card,
        .dashboard-stat-card,
        .outlet-map-card,
        .outlet-list-card {
            border: 0;
            border-radius: 8px;
            box-shadow: 0 10px 24px rgba(35, 45, 66, .06);
        }

        .dashboard-stat-icon {
            align-items: center;
            border-radius: 8px;
            display: inline-flex;
            font-size: 22px;
            height: 46px;
            justify-content: center;
            width: 46px;
        }

        #outletMap {
            border-radius: 8px;
            height: 430px;
            min-height: 430px;
            overflow: hidden;
            width: 100%;
        }

        #salesOutletChart {
            min-height: 340px;
        }

        .outlet-list {
            max-height: 430px;
            overflow-y: auto;
        }

        .outlet-list-item {
            border: 1px solid #edf0f5;
            border-radius: 8px;
            padding: 12px;
        }

        .leaflet-popup-content {
            margin: 12px 14px;
        }
    </style>
@endpush

@section('content')
    <div class="dashboard-page">
        <div class="row">
            <div class="col-12">
                <div class="card dashboard-title-card mb-4">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h3 class="mb-1">Dashboard</h3>
                            <p class="text-muted mb-0">Overview outlet, penjualan hari ini, dan kondisi stok.</p>
                        </div>
                        <span class="badge bg-soft-primary text-primary px-3 py-2">
                            {{ now()->format('d M Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-xl-3">
                <div class="card dashboard-stat-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Outlet Aktif</p>
                            <h4 class="mb-0">{{ number_format($dashboardStats['active_outlets'], 0, ',', '.') }}</h4>
                        </div>
                        <span class="dashboard-stat-icon bg-soft-primary text-primary">
                            <i class="ri-store-2-line"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card dashboard-stat-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Produk Aktif</p>
                            <h4 class="mb-0">{{ number_format($dashboardStats['products'], 0, ',', '.') }}</h4>
                        </div>
                        <span class="dashboard-stat-icon bg-soft-info text-info">
                            <i class="ri-box-3-line"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card dashboard-stat-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Transaksi Hari Ini</p>
                            <h4 class="mb-0">{{ number_format($dashboardStats['today_transactions'], 0, ',', '.') }}</h4>
                        </div>
                        <span class="dashboard-stat-icon bg-soft-success text-success">
                            <i class="ri-shopping-cart-2-line"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card dashboard-stat-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Low Stock</p>
                            <h4 class="mb-0">{{ number_format($dashboardStats['low_stock'], 0, ',', '.') }}</h4>
                        </div>
                        <span class="dashboard-stat-icon bg-soft-warning text-warning">
                            <i class="ri-alert-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card outlet-map-card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h4 class="card-title mb-0">Sales per Cabang</h4>
                            <small class="text-muted">Revenue dan transaksi bulan {{ now()->translatedFormat('F Y') }}.</small>
                        </div>
                        <span class="badge bg-soft-primary text-primary">
                            {{ number_format(array_sum($salesChart['transactions']), 0, ',', '.') }} transaksi
                        </span>
                    </div>
                    <div class="card-body">
                        <div id="salesOutletChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card outlet-map-card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h4 class="card-title mb-0">Peta Outlet</h4>
                            <small class="text-muted">Map menggunakan Leaflet dan OpenStreetMap.</small>
                        </div>
                        <span class="badge bg-soft-success text-success">
                            Revenue hari ini Rp {{ number_format($dashboardStats['today_revenue'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div id="outletMap"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card outlet-list-card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Outlet</h4>
                        <small class="text-muted">Ringkasan stok per outlet.</small>
                    </div>
                    <div class="card-body outlet-list">
                        <div class="d-grid gap-3">
                            @forelse ($outlets as $outlet)
                                <div class="outlet-list-item">
                                    <div class="d-flex justify-content-between gap-3">
                                        <div>
                                            <h6 class="mb-1">{{ $outlet['name'] }}</h6>
                                            <small class="text-muted">{{ $outlet['code'] }} - {{ $outlet['address'] }}</small>
                                        </div>
                                        <span class="badge {{ $outlet['low_stock_count'] > 0 ? 'bg-warning' : 'bg-success' }}">
                                            {{ $outlet['low_stock_count'] > 0 ? 'Low' : 'OK' }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between mt-3">
                                        <span class="text-muted">Total stok</span>
                                        <strong>{{ number_format($outlet['stock'], 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">Belum ada outlet aktif.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const outlets = @json($outlets);
            const salesChart = @json($salesChart);
            const mapElement = document.getElementById('outletMap');
            const salesChartElement = document.getElementById('salesOutletChart');

            function renderSalesOutletChart() {
                if (!salesChartElement || typeof ApexCharts === 'undefined') {
                    return;
                }

                const chart = new ApexCharts(salesChartElement, {
                    chart: {
                        type: 'bar',
                        height: 340,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: 'Revenue',
                        data: salesChart.revenue
                    }],
                    colors: ['#3a57e8'],
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '46%',
                            distributed: false
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: salesChart.labels,
                        labels: {
                            rotate: -20,
                            trim: true
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(value) {
                                return 'Rp ' + Number(value).toLocaleString('id-ID');
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(value, options) {
                                const transactions = salesChart.transactions[options.dataPointIndex] || 0;
                                return 'Rp ' + Number(value).toLocaleString('id-ID') + ' - ' +
                                    Number(transactions).toLocaleString('id-ID') + ' transaksi';
                            }
                        }
                    },
                    noData: {
                        text: 'Belum ada data sales bulan ini'
                    },
                    grid: {
                        borderColor: '#edf0f5'
                    }
                });

                chart.render().catch(function(error) {
                    console.error('Sales outlet chart failed to render', error);
                    salesChartElement.innerHTML = '<div class="text-muted text-center py-5">Chart gagal dimuat.</div>';
                });
            }

            function loadApexCharts(callback) {
                if (typeof ApexCharts !== 'undefined') {
                    callback();
                    return;
                }

                if (window.SVG) {
                    window.__apbHopeSvg = window.SVG;
                    try {
                        delete window.SVG;
                    } catch (error) {
                        window.SVG = undefined;
                    }
                }

                const script = document.createElement('script');
                script.src = "{{ asset('assets/js/charts/apexcharts.js') }}";
                script.onload = callback;
                script.onerror = function() {
                    if (salesChartElement) {
                        salesChartElement.innerHTML = '<div class="text-muted text-center py-5">Chart gagal dimuat.</div>';
                    }
                };
                document.body.appendChild(script);
            }

            loadApexCharts(renderSalesOutletChart);

            if (!mapElement || typeof L === 'undefined') {
                return;
            }

            const map = L.map(mapElement, {
                scrollWheelZoom: false
            }).setView([-2.5489, 118.0149], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            const markers = [];

            outlets.forEach(function(outlet) {
                const marker = L.marker([outlet.lat, outlet.lng]).addTo(map);
                marker.bindPopup(`
                    <strong>${outlet.name}</strong><br>
                    Kode: ${outlet.code}<br>
                    Total stok: ${Number(outlet.stock).toLocaleString('id-ID')}<br>
                    Low stock: ${Number(outlet.low_stock_count).toLocaleString('id-ID')}<br>
                    <span class="text-muted">${outlet.address}</span>
                `);
                markers.push(marker);
            });

            if (markers.length > 0) {
                const group = L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.2));
            }

            setTimeout(function() {
                map.invalidateSize();
            }, 250);
        });
    </script>
@endpush
