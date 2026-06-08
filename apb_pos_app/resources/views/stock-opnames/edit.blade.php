@extends('layouts.app')

@section('title', 'Edit Stock Opname')

@section('content')
    <form action="{{ route('stock-opnames.update', $encryptedId) }}" method="POST" id="opnameForm">
        @csrf
        @method('PUT')
        @include('stock-opnames.form')
    </form>
@endsection
