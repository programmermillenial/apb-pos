@extends('layouts.app')

@section('title', 'Edit Stock Transfer')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <form action="{{ route('stock-transfers.update', $encryptedId) }}" method="POST" id="transferForm">
                @csrf
                @method('PUT')

                @include('stock-transfers.form')

            </form>
        </div>
    </div>
@endsection
