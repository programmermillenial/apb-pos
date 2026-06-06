@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Purchase Order</h4>
                        <small class="text-muted">Data purchase order</small>
                    </div>

                    <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
                        <i class="ri-add-line"></i> Tambah Data
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered table-striped align-middle w-100">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>No PO</th>
                                    <th>Tanggal</th>
                                    <th>Outlet</th>
                                    <th>Supplier</th>
                                    <th>Grand Total</th>
                                    <th>Status</th>
                                    <th width="12%" class="text-center">Aksi</th>
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
                searchPlaceholder: "Cari data..."
            },
            ajax: "{{ route('purchase-orders.datatable') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'po_number',
                    name: 'po_number'
                },
                {
                    data: 'po_date',
                    name: 'po_date'
                },
                {
                    data: 'outlet_name',
                    name: 'outlet.name'
                },
                {
                    data: 'supplier_name',
                    name: 'supplier.name'
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
