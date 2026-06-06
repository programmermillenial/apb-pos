@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Tambah Supplier</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf

                @include('suppliers.form', [
                    'supplier' => null,
                    'button' => 'Simpan',
                ])
            </form>
        </div>
    </div>
@endsection
