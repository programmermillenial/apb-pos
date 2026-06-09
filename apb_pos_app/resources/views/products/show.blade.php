@extends('layouts.app')

@section('title', 'Detail Product')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Detail Produk</h4>

                    <a href="{{ route('products.index') }}" class="btn btn-light">
                        Back
                    </a>
                </div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="250">SKU</th>
                            <td>{{ $product->sku }}</td>
                        </tr>
                        <tr>
                            <th>Barcode</th>
                            <td>{{ $product->barcode ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{ $product->name }}</td>
                        </tr>
                        <tr>
                            <th>Stock</th>
                            <td>{{ $product->getTotalStock() ?? 0 }}</td>
                        </tr>
                        <tr>
                            <th>Outlets</th>
                            <td>
                                @if($product->productOutlets->count() > 0)
                                    <ul class="mb-0">
                                        @foreach($product->productOutlets as $po)
                                            <li>{{ $po->outlet->name }} - Stock: {{ number_format($po->stock, 0, ',', '.') }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td>{{ $product->category->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Brand</th>
                            <td>{{ $product->brand->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Unit</th>
                            <td>{{ $product->unit->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Weight</th>
                            <td>{{ $product->weight ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Cost Price</th>
                            <td>Rp {{ number_format($product->cost_price, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Sell Price</th>
                            <td>Rp {{ number_format($product->sell_price, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Min Stock</th>
                            <td>{{ $product->min_stock }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if ($product->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $product->description ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection
