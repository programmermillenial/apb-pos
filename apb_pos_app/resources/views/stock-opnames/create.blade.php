@extends('layouts.app')

@section('title', 'Buat Stock Opname Baru')

@section('content')
    <form action="{{ route('stock-opnames.store') }}" method="POST" id="opnameForm">
        @csrf
        @include('stock-opnames.form')
    </form>
@endsection
