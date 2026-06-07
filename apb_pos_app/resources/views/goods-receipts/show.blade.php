@extends('layouts.app')

@section('content')
    <div class="conatiner-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Goods Receipt Detail</h4>
                            <small class="text-muted">{{ $goodsReceipt->gr_number }}</small>
                        </div>

                        <a href="{{ route('goods-receipts.index') }}" class="btn btn-light">
                            Back
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="row mb-4">

                            <div class="col-md-4">
                                <label class="text-muted">GR Number</label>
                                <h6>{{ $goodsReceipt->gr_number }}</h6>
                            </div>

                            <div class="col-md-4">
                                <label class="text-muted">PO Number</label>
                                <h6>{{ $goodsReceipt->purchaseOrder->po_number ?? '-' }}</h6>
                            </div>

                            <div class="col-md-4">
                                <label class="text-muted">Receipt Date</label>
                                <h6>{{ date('d M Y', strtotime($goodsReceipt->receipt_date)) }}</h6>
                            </div>

                            <div class="col-md-4 mt-3">
                                <label class="text-muted">Received By</label>
                                <h6>{{ $goodsReceipt->received_by ?? '-' }}</h6>
                            </div>

                            <div class="col-md-4 mt-3">
                                <label class="text-muted">Status</label>
                                <h6>
                                    <span class="badge bg-success">{{ ucfirst($goodsReceipt->status) }}</span>
                                </h6>
                            </div>

                            <div class="col-md-12 mt-3">
                                <label class="text-muted">Notes</label>
                                <p class="mb-0">{{ $goodsReceipt->notes ?? '-' }}</p>
                            </div>

                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-transaction">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Product</th>
                                        <th class="text-end">Ordered Qty</th>
                                        <th class="text-end">Received Qty</th>
                                        <th class="text-end">Cost Price</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $grandTotal = 0;
                                    @endphp

                                    @foreach ($goodsReceipt->details as $item)
                                        @php
                                            $grandTotal += $item->subtotal;
                                        @endphp

                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $item->product->name ?? '-' }}</td>
                                            <td class="text-end">{{ number_format($item->ordered_qty) }}</td>
                                            <td class="text-end">{{ number_format($item->received_qty) }}</td>
                                            <td class="text-end">Rp {{ number_format($item->cost_price, 0, ',', '.') }}
                                            </td>
                                            <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <th colspan="5" class="text-end">TOTAL</th>
                                        <th class="text-end">Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
