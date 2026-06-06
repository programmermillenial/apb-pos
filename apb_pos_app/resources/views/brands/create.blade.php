@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Tambah Brand</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('brands.store') }}" method="POST">
                        @csrf

                        @include('brands.form', [
                            'brand' => null,
                            'button' => 'Simpan',
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
