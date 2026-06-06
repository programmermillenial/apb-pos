@extends('layouts.app')

@section('title', 'Create Product')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header">
                    <h4 class="card-title mb-0">Tambah Produk</h4>
                </div>

                <form action="{{ route('products.store') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        @include('products.form', [
                            'product' => null,
                            'button' => 'Simpan',
                        ])
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
