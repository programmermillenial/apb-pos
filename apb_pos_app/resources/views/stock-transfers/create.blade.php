@extends('layouts.app')

@section('title', 'Stock Transfer Baru')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <form action="{{ route('stock-transfers.store') }}" method="POST" id="transferForm">
                @csrf

                @include('stock-transfers.form')

            </form>
        </div>
    </div>
@endsection
