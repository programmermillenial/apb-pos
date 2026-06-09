@extends('layouts.app')

@section('title', 'Stock Transfer')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Stock Transfer</h4>
                        <small class="text-muted">History transfer stock antar outlet</small>
                    </div>

                    <a href="{{ route('stock-transfers.create') }}" class="btn btn-primary">
                        <i class="ri-add-line"></i> Transfer Baru
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered table-striped align-middle w-100">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>No Transfer</th>
                                    <th>Tanggal</th>
                                    <th>Dari Outlet</th>
                                    <th>Ke Outlet</th>
                                    <th>Total Item</th>
                                    <th>Status</th>
                                    <th>Dibuat Oleh</th>
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
            ajax: "{{ route('stock-transfers.datatable') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'transfer_no',
                    name: 'transfer_no'
                },
                {
                    data: 'transfer_date',
                    name: 'transfer_date',
                    searchable: false
                },
                {
                    data: 'from_outlet_name',
                    name: 'from_outlet_name'
                },
                {
                    data: 'to_outlet_name',
                    name: 'to_outlet_name'
                },
                {
                    data: 'total_items',
                    name: 'total_items',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'status_badge',
                    name: 'status',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'creator_name',
                    name: 'creator_name'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    </script>
@endpush
