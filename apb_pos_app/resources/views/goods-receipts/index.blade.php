@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">
                            Goods Receipt
                        </h4>
                        <small class="text-muted">
                            Daftar penerimaan barang dari Purchase Order
                        </small>
                    </div>

                    <a href="{{ route('goods-receipts.create') }}" class="btn btn-primary">
                        <i class="ri-add-line"></i> Tambah Data
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover align-middle w-100">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>GR Number</th>
                                    <th>PO Number</th>
                                    <th>Receipt Date</th>
                                    <th>Received By</th>
                                    <th>Status</th>
                                    <th width="10%">Action</th>
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

            ajax: "{{ route('goods-receipts.datatable') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'gr_number',
                    name: 'gr_number'
                },
                {
                    data: 'po_number',
                    name: 'po_number'
                },
                {
                    data: 'receipt_date',
                    name: 'receipt_date'
                },
                {
                    data: 'received_by',
                    name: 'received_by'
                },
                {
                    data: 'status_badge',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            columnDefs: [{
                targets: [0, 5, 6],
                className: 'text-center'
            }]
        });
    </script>
@endpush
