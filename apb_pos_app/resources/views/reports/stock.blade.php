@extends('layouts.app')

@section('title', 'Stock Report')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Stock Report</h4>
                        <small class="text-muted">Posisi stok produk per outlet</small>
                    </div>
                    <a href="{{ route('reports.stock.csv', request()->query()) }}" class="btn btn-success">
                        <i class="ri-download-2-line"></i> Export CSV
                    </a>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('reports.stock') }}" class="row g-3 align-items-end mb-4">
                        <div class="col-md-5">
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
                        <div class="col-md-5">
                            <label class="form-label">Status Stok</label>
                            <select name="stock_status" class="form-select">
                                <option value="all" @selected($stockStatus === 'all')>Semua</option>
                                <option value="available" @selected($stockStatus === 'available')>Available</option>
                                <option value="low" @selected($stockStatus === 'low')>Low Stock</option>
                                <option value="empty" @selected($stockStatus === 'empty')>Empty</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button class="btn btn-primary" type="submit">
                                <i class="ri-filter-3-line"></i> Filter
                            </button>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-md-2">
                            <div class="bg-soft-primary rounded p-3 mb-3">
                                <small class="text-muted">Produk</small>
                                <h4 class="mb-0">{{ number_format($summary['products'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="bg-soft-info rounded p-3 mb-3">
                                <small class="text-muted">Total Stok</small>
                                <h4 class="mb-0">{{ number_format($summary['total_stock'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="bg-soft-warning rounded p-3 mb-3">
                                <small class="text-muted">Low Stock</small>
                                <h4 class="mb-0">{{ number_format($summary['low_stock'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="bg-soft-danger rounded p-3 mb-3">
                                <small class="text-muted">Empty</small>
                                <h4 class="mb-0">{{ number_format($summary['empty_stock'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="bg-soft-secondary rounded p-3 mb-3">
                                <small class="text-muted">Nilai HPP</small>
                                <h4 class="mb-0">Rp {{ number_format($summary['stock_value_cost'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="bg-soft-success rounded p-3 mb-3">
                                <small class="text-muted">Nilai Jual</small>
                                <h4 class="mb-0">Rp {{ number_format($summary['stock_value_sell'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Outlet</th>
                                    <th>SKU</th>
                                    <th>Produk</th>
                                    <th>Kategori</th>
                                    <th>Brand</th>
                                    <th class="text-end">Stok</th>
                                    <th class="text-end">ROP</th>
                                    <th class="text-end">HPP</th>
                                    <th class="text-end">Harga Jual</th>
                                    <th class="text-end">Nilai HPP</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stocks as $stock)
                                    @php
                                        $status = $stock->stock <= 0 ? 'Empty' : ($stock->stock <= $stock->reorder_point ? 'Low Stock' : 'Available');
                                        $badge = $status === 'Empty' ? 'danger' : ($status === 'Low Stock' ? 'warning' : 'success');
                                    @endphp
                                    <tr>
                                        <td>{{ $stock->outlet_name }}</td>
                                        <td>{{ $stock->sku }}</td>
                                        <td>{{ $stock->product_name }}</td>
                                        <td>{{ $stock->category_name ?? '-' }}</td>
                                        <td>{{ $stock->brand_name ?? '-' }}</td>
                                        <td class="text-end fw-bold">{{ number_format($stock->stock, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($stock->reorder_point, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($stock->cost_price, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($stock->sell_price, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($stock->stock_value_cost, 0, ',', '.') }}</td>
                                        <td><span class="badge bg-{{ $badge }}">{{ $status }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">Belum ada data stok.</td>
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
