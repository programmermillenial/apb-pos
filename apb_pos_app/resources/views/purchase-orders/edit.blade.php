@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Edit Supplier</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('purchase-orders.update', $id) }}" method="POST">
                @csrf
                @method('PUT')

                @include('purchase-orders.form', [
                    'purchaseOrder' => $purchaseOrder,
                    'button' => 'Update',
                ])

            </form>
        </div>
    </div>
@endsection
