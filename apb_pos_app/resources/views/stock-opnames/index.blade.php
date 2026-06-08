@extends('layouts.app')

@section('title', 'Stock Opname')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Stock Opname</h4>
                        <small class="text-muted">Stock take / physical inventory count</small>
                    </div>

                    <a href="{{ route('stock-opnames.create') }}" class="btn btn-primary">
                        <i class="ri-add-line"></i> Opname Baru
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered table-striped align-middle w-100">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>No Opname</th>
                                    <th>Tanggal</th>
                                    <th>Outlet</th>
                                    <th>Tipe</th>
                                    <th>Total Item</th>
                                    <th>Status</th>
                                    <th>PIC</th>
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
            ajax: "{{ route('stock-opnames.datatable') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'opname_no',
                    name: 'opname_no'
                },
                {
                    data: 'opname_date',
                    name: 'opname_date'
                },
                {
                    data: 'outlet_name',
                    name: 'outlet.name'
                },
                {
                    data: 'type_badge',
                    name: 'type',
                    className: 'text-center'
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
                    data: 'pic_name',
                    name: 'pic_name'
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
