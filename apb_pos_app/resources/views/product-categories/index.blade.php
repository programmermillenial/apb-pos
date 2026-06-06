@extends('layouts.app')

@section('title', 'Product Category')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">
                            Kategori Produk
                        </h4>
                        <small class="text-muted">
                            Master data kategori produk
                        </small>
                    </div>

                    <a href="{{ route('product-categories.create') }}" class="btn btn-primary">
                        <i class="ri-add-line"></i>
                        Tambah Data
                    </a>

                </div>

                <div class="card-body">
                    <table id="datatable" class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th width="120">Status</th>
                                <th width="120">Action</th>
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
        $(document).ready(function() {
            table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,

                language: {
                    search: "",
                    searchPlaceholder: "Cari data..."
                },

                ajax: "{{ route('product-categories.datatable') }}",

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    }
                ],
                columnDefs: [{
                    targets: [0, 3, 4],
                    className: 'text-center'
                }]
            });
        });
    </script>
@endpush
