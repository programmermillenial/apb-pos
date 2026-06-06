@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="card-title mb-0">
                    User
                </h4>
                <small class="text-muted">
                    Master data user
                </small>
            </div>

            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="ri-add-line"></i> Tambah Data
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle w-100">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Outlet</th>
                            <th width="12%" class="text-center">Role</th>
                            <th width="10%" class="text-center">Status</th>
                            <th width="12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                </table>
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
            ajax: "{{ route('users.datatable') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'username',
                    name: 'username'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'outlet_name',
                    name: 'outlet.name'
                },
                {
                    data: 'role_badge',
                    name: 'role',
                    className: 'text-center'
                },
                {
                    data: 'status_badge',
                    name: 'is_active',
                    className: 'text-center'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
            ],
            columnDefs: [{
                targets: [0, 5, 6, 7],
                className: 'text-center'
            }]
        });
    </script>
@endpush
