@extends('layouts.app')

@section('title', 'Profit Report')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Profit Report</h4>
                        <small class="text-muted">Estimasi profit berdasarkan HPP produk saat ini</small>
                    </div>
                    <a href="{{ route('reports.profit.csv', request()->query()) }}" class="btn btn-success">
                        <i class="ri-download-2-line"></i> Export CSV
                    </a>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('reports.profit') }}" class="row g-3 align-items-end mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Outlet</label>
                            <select name="outlet_id" class="form-select">
                                <option value="">Semua Outlet</option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}" @selected(request('outlet_id') == $outlet->id)>
                                        {{ $outlet->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button class="btn btn-primary" type="submit">
                                <i class="ri-filter-3-line"></i> Filter
                            </button>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="bg-soft-primary rounded p-3 mb-3">
                                <small class="text-muted">Qty Terjual</small>
                                <h4 class="mb-0">{{ number_format($summary['qty'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-soft-info rounded p-3 mb-3">
                                <small class="text-muted">Revenue</small>
                                <h4 class="mb-0">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-soft-warning rounded p-3 mb-3">
                                <small class="text-muted">Cost</small>
                                <h4 class="mb-0">Rp {{ number_format($summary['cost'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-soft-success rounded p-3 mb-3">
                                <small class="text-muted">Gross Profit</small>
                                <h4 class="mb-0">Rp {{ number_format($summary['profit'], 0, ',', '.') }}</h4>
                                <small>Margin {{ number_format($summary['margin'], 2, ',', '.') }}%</small>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3">Profit per Produk</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Produk</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Revenue</th>
                                    <th class="text-end">Cost</th>
                                    <th class="text-end">Profit</th>
                                    <th class="text-end">Margin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($productSummary as $item)
                                    <tr>
                                        <td>{{ $item['sku'] ?? '-' }}</td>
                                        <td>{{ $item['product_name'] }}</td>
                                        <td class="text-end">{{ number_format($item['qty'], 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($item['revenue'], 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($item['cost'], 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($item['profit'], 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($item['margin'], 2, ',', '.') }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Belum ada data profit.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mb-3">Detail Transaksi</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Tanggal</th>
                                    <th>Outlet</th>
                                    <th>SKU</th>
                                    <th>Produk</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Harga Jual</th>
                                    <th class="text-end">HPP</th>
                                    <th class="text-end">Revenue</th>
                                    <th class="text-end">Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($details as $detail)
                                    <tr>
                                        <td>{{ $detail->invoice_number }}</td>
                                        <td>{{ \Carbon\Carbon::parse($detail->sale_date)->format('d/m/Y') }}</td>
                                        <td>{{ $detail->outlet_name }}</td>
                                        <td>{{ $detail->sku ?? '-' }}</td>
                                        <td>{{ $detail->product_name }}</td>
                                        <td class="text-end">{{ number_format($detail->qty, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($detail->sell_price, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($detail->cost_price, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($detail->revenue, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($detail->gross_profit, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Belum ada data profit.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
