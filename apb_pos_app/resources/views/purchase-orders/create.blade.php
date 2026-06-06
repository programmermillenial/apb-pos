@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Tambah Purchase Order</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('purchase-orders.store') }}" method="POST">
                @csrf

                @include('purchase-orders.form', [
                    'purchaseOrder' => null,
                    'button' => 'Simpan',
                ])
            </form>
        </div>
    </div>
@endsection
