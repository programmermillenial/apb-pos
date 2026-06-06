@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Tambah Customer</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('customers.store') }}" method="POST">
                        @csrf

                        @include('customers.form', [
                            'customer' => null,
                            'button' => 'Simpan',
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
