@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
    @php
        $encryptedId = Crypt::encryptString($sale->id);
    @endphp

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Detail Transaksi</h4>
                        <small class="text-muted">{{ $sale->invoice_number }}</small>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('sales.receipt', $encryptedId) }}" class="btn btn-primary">
                            <i class="ri-printer-line"></i> Print Struk
                        </a>
                        <a href="{{ route('sales.receipt-pdf', $encryptedId) }}" class="btn btn-success">
                            <i class="ri-file-pdf-line"></i> PDF
                        </a>
                        <a href="{{ route('sales.history') }}" class="btn btn-light">
                            Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%">Invoice</th>
                                    <td>: {{ $sale->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>: {{ $sale->sale_date ? $sale->sale_date->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Outlet</th>
                                    <td>: {{ $sale->outlet->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Customer</th>
                                    <td>: {{ $sale->customer->name ?? 'Umum' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%">Kasir</th>
                                    <td>: {{ $sale->creator->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Metode Bayar</th>
                                    <td>: {{ strtoupper($sale->payment_method) }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>:
                                        @if ($sale->status === 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif ($sale->status === 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @else
                                            <span class="badge bg-light text-dark">{{ ucfirst($sale->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dibuat</th>
                                    <td>: {{ $sale->created_at ? $sale->created_at->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle table-transaction transaction-detail-table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Produk</th>
                                    <th>SKU</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Diskon</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sale->saleDetails as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ $item->sku ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($item->qty, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($item->discount_amount, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="po-summary-row">
                                    <th colspan="5"></th>
                                    <th class="text-end po-summary-label">Subtotal</th>
                                    <th class="text-end po-summary-value">
                                        Rp {{ number_format($sale->subtotal, 0, ',', '.') }}
                                    </th>
                                </tr>

                                <tr class="po-summary-row">
                                    <th colspan="5"></th>
                                    <th class="text-end po-summary-label">Diskon</th>
                                    <th class="text-end po-summary-value text-danger">
                                        Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}
                                    </th>
                                </tr>

                                <tr class="po-summary-row">
                                    <th colspan="5"></th>
                                    <th class="text-end po-summary-label">Pajak</th>
                                    <th class="text-end po-summary-value">
                                        Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}
                                    </th>
                                </tr>

                                <tr class="po-grand-total-row">
                                    <th colspan="5"></th>
                                    <th class="text-end po-summary-label">Grand Total</th>
                                    <th class="text-end po-summary-value">
                                        Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                                    </th>
                                </tr>

                                <tr class="po-summary-row">
                                    <th colspan="5"></th>
                                    <th class="text-end po-summary-label">Bayar</th>
                                    <th class="text-end po-summary-value">
                                        Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}
                                    </th>
                                </tr>

                                <tr class="po-summary-row">
                                    <th colspan="5"></th>
                                    <th class="text-end po-summary-label">Kembalian</th>
                                    <th class="text-end po-summary-value">
                                        Rp {{ number_format($sale->change_amount, 0, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if ($sale->note)
                        <div class="mt-3">
                            <strong>Catatan:</strong>
                            <p>{{ $sale->note }}</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
