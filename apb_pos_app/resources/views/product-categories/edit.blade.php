@extends('layouts.app')

@section('title', 'Edit Product Category')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-header">
                    <h4 class="card-title mb-0">Edit Product Category</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('product-categories.update', $id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @include('product-categories.form', [
                            'productCategory' => $productCategory,
                            'button' => 'Update',
                        ])
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
