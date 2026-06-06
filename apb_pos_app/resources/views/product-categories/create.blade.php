@extends('layouts.app')

@section('title', 'Tambah Product Category')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-header">
                <h4 class="card-title mb-0">Tambah Product Category</h4>
            </div>

            <div class="card-body">
                <form action="{{ route('product-categories.store') }}" method="POST">
                    @csrf

                    @include('product-categories.form', [
                            'productCategory' => null,
                            'button' => 'Simpan',
                        ])
                </form>
            </div>

        </div>
    </div>
</div>
@endsection