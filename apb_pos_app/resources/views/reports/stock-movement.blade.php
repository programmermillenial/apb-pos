@extends('layouts.app')

@section('title', 'Stock Movement Report')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Stock Movement Report</h4>
                        <small class="text-muted">Riwayat keluar masuk stok per transaksi</small>
                    </div>
                    <a href="{{ route('reports.stock-movement.csv', request()->query()) }}" class="btn btn-success">
                        <i class="ri-download-2-line"></i> Export CSV
                    </a>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('reports.stock-movement') }}" class="row g-3 align-items-end mb-4">
                        <div class="col-md-2">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-2">
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
                            <label class="form-label">Tipe</label>
                            <select name="type" class="form-select">
                                <option value="">Semua</option>
                                @foreach (['IN', 'OUT', 'ADJUSTMENT'] as $type)
                                    <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Source</label>
                            <select name="source_type" class="form-select">
                                <option value="">Semua</option>
                                @foreach ($sourceTypes as $sourceType)
                                    <option value="{{ $sourceType }}" @selected(request('source_type') === $sourceType)>
                                        {{ $sourceType }}
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
                                <small class="text-muted">Movement</small>
                                <h4 class="mb-0">{{ number_format($summary['movements'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-soft-success rounded p-3 mb-3">
                                <small class="text-muted">Qty In</small>
                                <h4 class="mb-0">{{ number_format($summary['qty_in'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-soft-danger rounded p-3 mb-3">
                                <small class="text-muted">Qty Out</small>
                                <h4 class="mb-0">{{ number_format($summary['qty_out'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-soft-info rounded p-3 mb-3">
                                <small class="text-muted">Net Qty</small>
                                <h4 class="mb-0">{{ number_format($summary['net_qty'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Outlet</th>
                                    <th>SKU</th>
                                    <th>Produk</th>
                                    <th>Tipe</th>
                                    <th>Source</th>
                                    <th>Reference</th>
                                    <th class="text-end">In</th>
                                    <th class="text-end">Out</th>
                                    <th class="text-end">Balance</th>
                                    <th>User</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($movements as $movement)
                                    @php
                                        $badge = $movement->type === 'IN' ? 'success' : ($movement->type === 'OUT' ? 'danger' : 'warning');
                                    @endphp
                                    <tr>
                                        <td>{{ $movement->movement_date?->format('d/m/Y') }}</td>
                                        <td>{{ $movement->outlet->name ?? '-' }}</td>
                                        <td>{{ $movement->product->sku ?? '-' }}</td>
                                        <td>{{ $movement->product->name ?? '-' }}</td>
                                        <td><span class="badge bg-{{ $badge }}">{{ $movement->type }}</span></td>
                                        <td>{{ $movement->source_type ?? '-' }}</td>
                                        <td>{{ $movement->reference_no ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($movement->qty_in, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($movement->qty_out, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">{{ number_format($movement->balance, 0, ',', '.') }}</td>
                                        <td>{{ $movement->user->name ?? '-' }}</td>
                                        <td>{{ $movement->note ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center text-muted">Belum ada data movement.</td>
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
