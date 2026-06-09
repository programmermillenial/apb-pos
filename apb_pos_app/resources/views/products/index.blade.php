@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Produk</h4>
                        <small class="text-muted">Data master produk</small>
                    </div>

                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <i class="ri-add-line"></i> Tambah Data
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered table-striped align-middle w-100">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Unit</th>
                                    <th>Stock</th>
                                    <th>Sell Price</th>
                                    <th>Status</th>
                                    <th width="130" class="text-center">Action</th>
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
        $(function() {
            table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                language: {
                    search: "",
                    searchPlaceholder: "Cari data..."
                },
                ajax: "{{ route('products.datatable') }}",
                columns: [{
                        data: 'sku',
                        name: 'sku'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category',
                        name: 'product_category.name',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'brand',
                        name: 'brand.name',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'unit',
                        name: 'unit.name',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'stock',
                        name: 'stock'
                    },
                    {
                        data: 'sell_price',
                        name: 'sell_price'
                    },
                    {
                        data: 'status',
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
                    },
                ],
                columnDefs: [{
                    targets: [6, 7],
                    className: 'text-center'
                }]
            });
        });
    </script>
@endpush
