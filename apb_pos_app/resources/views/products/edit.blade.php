@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header">
                    <h4 class="card-title mb-0">Edit Produk</h4>
                </div>

                <form action="{{ route('products.update', $id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        @include('products.form', [
                            'product' => $product,
                            'button' => 'Update',
                        ])
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
