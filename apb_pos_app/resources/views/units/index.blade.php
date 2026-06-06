@extends('layouts.app')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">
                        Unit
                    </h4>
                    <small class="text-muted">
                        Master data unit
                    </small>
                </div>

                <a href="{{ route('units.create') }}" class="btn btn-primary">
                    <i class="ri-add-line"></i> Tambah Data
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Unit</th>
                                <th>Singkatan</th>
                                <th>Deskripsi</th>
                                <th width="100">Status</th>
                                <th width="120" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                    </table>
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
            ajax: "{{ route('units.datatable') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'short_name',
                    name: 'short_name'
                },
                {
                    data: 'description',
                    name: 'description',
                    defaultContent: '-'
                },
                {
                    data: 'is_active',
                    name: 'is_active',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }
            ],
            columnDefs: [{
                targets: [0, 4, 5],
                className: 'text-center'
            }]
        });
    </script>
@endpush
