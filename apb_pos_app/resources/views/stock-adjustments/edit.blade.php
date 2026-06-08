@extends('layouts.app')

@section('title', 'Edit Stock Adjustment')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <form action="{{ route('stock-adjustments.update', $encryptedId) }}" method="POST" id="adjustmentForm">
                @csrf
                @method('PUT')

                @include('stock-adjustments.form')

            </form>
        </div>
    </div>
@endsection
