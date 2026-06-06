@extends('layouts.app')

@section('content')
    @php
        $encryptedId = Crypt::encryptString($purchaseOrder->id);
    @endphp

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Detail Purchase Order</h4>

                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-light">
                        Kembali
                    </a>
                </div>

                <div class="card-body">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%">No PO</th>
                                    <td>: {{ $purchaseOrder->po_number }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal PO</th>
                                    <td>: {{ date('d/m/Y', strtotime($purchaseOrder->po_date)) }}</td>
                                </tr>
                                <tr>
                                    <th>Estimasi Datang</th>
                                    <td>:
                                        {{ $purchaseOrder->expected_date ? date('d/m/Y', strtotime($purchaseOrder->expected_date)) : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Outlet</th>
                                    <td>: {{ $purchaseOrder->outlet->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Supplier</th>
                                    <td>: {{ $purchaseOrder->supplier->name ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%">Status</th>
                                    <td>
                                        :
                                        @if ($purchaseOrder->status === 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif ($purchaseOrder->status === 'submitted')
                                            <span class="badge bg-info">Submitted</span>
                                        @elseif ($purchaseOrder->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif ($purchaseOrder->status === 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @elseif ($purchaseOrder->status === 'received')
                                            <span class="badge bg-primary">Received</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dibuat Oleh</th>
                                    <td>: {{ $purchaseOrder->creator->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Approved Oleh</th>
                                    <td>: {{ $purchaseOrder->approver->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Approved At</th>
                                    <td>:
                                        {{ $purchaseOrder->approved_at ? date('d/m/Y H:i', strtotime($purchaseOrder->approved_at)) : '-' }}
                                    </td>
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
                                @foreach ($purchaseOrder->purchaseOrderDetails as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ $item->sku ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($item->qty, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($item->discount_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="po-summary-row">
                                    <th colspan="5"></th>
                                    <th class="text-end po-summary-label">Subtotal</th>
                                    <th class="text-end po-summary-value">
                                        Rp {{ number_format($purchaseOrder->subtotal, 0, ',', '.') }}
                                    </th>
                                </tr>

                                <tr class="po-summary-row">
                                    <th colspan="5"></th>
                                    <th class="text-end po-summary-label">Diskon</th>
                                    <th class="text-end po-summary-value text-danger">
                                        Rp {{ number_format($purchaseOrder->discount_amount, 0, ',', '.') }}
                                    </th>
                                </tr>

                                <tr class="po-summary-row">
                                    <th colspan="5"></th>
                                    <th class="text-end po-summary-label">Pajak</th>
                                    <th class="text-end po-summary-value">
                                        Rp {{ number_format($purchaseOrder->tax_amount, 0, ',', '.') }}
                                    </th>
                                </tr>

                                <tr class="po-grand-total-row">
                                    <th colspan="5"></th>
                                    <th class="text-end po-summary-label">Grand Total</th>
                                    <th class="text-end po-summary-value">
                                        Rp {{ number_format($purchaseOrder->grand_total, 0, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if ($purchaseOrder->note)
                        <div class="mt-3">
                            <strong>Catatan:</strong>
                            <p>{{ $purchaseOrder->note }}</p>
                        </div>
                    @endif

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        @if ($purchaseOrder->status === 'draft')
                            <form action="{{ route('purchase-orders.submit', $encryptedId) }}" method="POST"
                                class="form-action">
                                @csrf
                                <button type="submit" class="btn btn-info">
                                    <i class="ri-send-plane-line"></i> Submit PO
                                </button>
                            </form>
                        @endif

                        @if ($purchaseOrder->status === 'submitted')
                            <form action="{{ route('purchase-orders.approve', $encryptedId) }}" method="POST"
                                class="form-action">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="ri-check-line"></i> Approve PO
                                </button>
                            </form>
                        @endif

                        @if (!in_array($purchaseOrder->status, ['cancelled', 'received']))
                            <form action="{{ route('purchase-orders.cancel', $encryptedId) }}" method="POST"
                                class="form-action-cancel">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="ri-close-line"></i> Cancel PO
                                </button>
                            </form>
                        @endif
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('.form-action').on('submit', function(e) {
            e.preventDefault();

            let form = this;

            Swal.fire({
                title: 'Lanjutkan proses?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        $('.form-action-cancel').on('submit', function(e) {
            e.preventDefault();

            let form = this;

            Swal.fire({
                title: 'Batalkan Purchase Order?',
                text: 'PO yang dibatalkan tidak bisa diproses lagi.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, batalkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
@endpush
