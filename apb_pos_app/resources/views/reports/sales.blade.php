@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Sales Report</h4>
                        <small class="text-muted">Ringkasan penjualan per periode</small>
                    </div>
                    <a href="{{ route('reports.sales.csv', request()->query()) }}" class="btn btn-success">
                        <i class="ri-download-2-line"></i> Export CSV
                    </a>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('reports.sales') }}" class="row g-3 align-items-end mb-4" data-report-filter>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-2">
                            <label class="form-label">Metode</label>
                            <select name="payment_method" class="form-select">
                                <option value="">Semua</option>
                                @foreach (['cash', 'transfer', 'qris', 'debit', 'credit'] as $method)
                                    <option value="{{ $method }}" @selected(request('payment_method') === $method)>
                                        {{ strtoupper($method) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 d-grid">
                            <button class="btn btn-primary" type="submit">
                                <i class="ri-filter-3-line"></i>
                            </button>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="bg-soft-primary rounded p-3 mb-3">
                                <small class="text-muted">Transaksi</small>
                                <h4 class="mb-0">{{ number_format($summary['transactions'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-soft-info rounded p-3 mb-3">
                                <small class="text-muted">Subtotal</small>
                                <h4 class="mb-0">Rp {{ number_format($summary['subtotal'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-soft-warning rounded p-3 mb-3">
                                <small class="text-muted">Diskon + Pajak</small>
                                <h4 class="mb-0">Rp {{ number_format($summary['discount'] + $summary['tax'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-soft-success rounded p-3 mb-3">
                                <small class="text-muted">Grand Total</small>
                                <h4 class="mb-0">Rp {{ number_format($summary['grand_total'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Metode Pembayaran</th>
                                    <th class="text-end">Transaksi</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($paymentSummary as $method => $item)
                                    <tr>
                                        <td>{{ strtoupper($method) }}</td>
                                        <td class="text-end">{{ number_format($item['count'], 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Belum ada data penjualan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Tanggal</th>
                                    <th>Outlet</th>
                                    <th>Customer</th>
                                    <th>Kasir</th>
                                    <th>Metode</th>
                                    <th class="text-end">Subtotal</th>
                                    <th class="text-end">Diskon</th>
                                    <th class="text-end">Pajak</th>
                                    <th class="text-end">Grand Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->invoice_number }}</td>
                                        <td>{{ $sale->sale_date?->format('d/m/Y') }}</td>
                                        <td>{{ $sale->outlet->name ?? '-' }}</td>
                                        <td>{{ $sale->customer->name ?? 'Umum' }}</td>
                                        <td>{{ $sale->creator->name ?? '-' }}</td>
                                        <td>{{ strtoupper($sale->payment_method) }}</td>
                                        <td class="text-end">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Belum ada data penjualan.</td>
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
