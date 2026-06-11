@extends('layouts.app')

@section('title', 'Stock Card')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Stock Card</h4>
                    <small class="text-muted">Kartu stok per produk dan outlet</small>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('reports.stock-card') }}" class="row g-3 align-items-end mb-4" data-report-filter>
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
                            <select name="outlet_id" class="form-select" required>
                                <option value="">Pilih Outlet</option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}" @selected(request('outlet_id') == $outlet->id)>
                                        {{ $outlet->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Produk</label>
                            <select name="product_id" class="form-select" required>
                                <option value="">Pilih Produk</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>
                                        {{ $product->sku }} - {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 d-grid">
                            <button class="btn btn-primary" type="submit">
                                <i class="ri-search-line"></i>
                            </button>
                        </div>
                    </form>

                    @if ($selectedProduct && $selectedOutlet)
                        <div class="row">
                            <div class="col-md-3">
                                <div class="bg-soft-primary rounded p-3 mb-3">
                                    <small class="text-muted">Produk</small>
                                    <h6 class="mb-0">{{ $selectedProduct->name }}</h6>
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
                                    <small class="text-muted">Ending Balance</small>
                                    <h4 class="mb-0">{{ number_format($summary['ending_balance'], 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Reference</th>
                                    <th>Type</th>
                                    <th>Source</th>
                                    <th class="text-end">In</th>
                                    <th class="text-end">Out</th>
                                    <th class="text-end">Balance</th>
                                    <th class="text-end">HPP</th>
                                    <th class="text-end">Harga Jual</th>
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
                                        <td>{{ $movement->reference_no ?? '-' }}</td>
                                        <td><span class="badge bg-{{ $badge }}">{{ $movement->type }}</span></td>
                                        <td>{{ $movement->source_type ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($movement->qty_in, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($movement->qty_out, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">{{ number_format($movement->balance, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($movement->cost_price, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($movement->sell_price, 0, ',', '.') }}</td>
                                        <td>{{ $movement->note ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">
                                            Pilih outlet dan produk untuk melihat kartu stok.
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
@endsection
