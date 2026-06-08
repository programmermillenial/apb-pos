@extends('layouts.app')

@section('title', 'Stock Adjustment')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Stock Adjustment</h4>
                        <small class="text-muted">History penyesuaian stock</small>
                    </div>

                    <a href="{{ route('stock-adjustments.create') }}" class="btn btn-primary">
                        <i class="ri-add-line"></i> Adjustment Baru
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered table-striped align-middle w-100">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>No Adjustment</th>
                                    <th>Tanggal</th>
                                    <th>Outlet</th>
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
            ajax: "{{ route('stock-adjustments.datatable') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'adjustment_no',
                    name: 'adjustment_no'
                },
                {
                    data: 'adjustment_date',
                    name: 'adjustment_date'
                },
                {
                    data: 'outlet_name',
                    name: 'outlet.name'
                },
                {
                    data: 'total_items',
                    name: 'total_items',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'status_badge',
                    name: 'status',
                    className: 'text-center'
                },
                {
                    data: 'creator_name',
                    name: 'creator.name'
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
