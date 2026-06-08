@extends('layouts.app')

@section('title', 'Stock Adjustment Baru')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <form action="{{ route('stock-adjustments.store') }}" method="POST" id="adjustmentForm">
                @csrf

                @include('stock-adjustments.form')

            </form>
        </div>
    </div>
@endsection
