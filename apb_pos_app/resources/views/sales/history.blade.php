@extends('layouts.app')

@section('title', 'Sales History')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Sales History</h4>
                        <small class="text-muted">Daftar transaksi penjualan kasir</small>
                    </div>

                    <a href="{{ route('sales.index') }}" class="btn btn-primary">
                        <i class="ri-shopping-cart-line"></i> Transaksi Baru
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered table-striped align-middle w-100">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Invoice</th>
                                    <th>Tanggal</th>
                                    <th>Outlet</th>
                                    <th>Customer</th>
                                    <th>Kasir</th>
                                    <th>Metode</th>
                                    <th>Grand Total</th>
                                    <th>Status</th>
                                    <th width="90" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            language: {
                search: "",
                searchPlaceholder: "Cari invoice/customer..."
            },
            ajax: "{{ route('sales.datatable') }}",
            order: [[2, 'desc']],
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'invoice_number',
                    name: 'invoice_number'
                },
                {
                    data: 'sale_date',
                    name: 'sale_date'
                },
                {
                    data: 'outlet_name',
                    name: 'outlet.name'
                },
                {
                    data: 'customer_name',
                    name: 'customer.name',
                    orderable: false
                },
                {
                    data: 'cashier_name',
                    name: 'creator.name',
                    orderable: false
                },
                {
                    data: 'payment_method',
                    name: 'payment_method',
                    className: 'text-center'
                },
                {
                    data: 'grand_total',
                    name: 'grand_total',
                    className: 'text-end'
                },
                {
                    data: 'status_badge',
                    name: 'status',
                    className: 'text-center'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
            ]
        });
    </script>
@endpush
