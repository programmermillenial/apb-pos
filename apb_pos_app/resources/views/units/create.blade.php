@extends('layouts.app')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Tambah Unit</h4>
            </div>

            <div class="card-body">
                <form action="{{ route('units.store') }}" method="POST">
                    @csrf

                    @include('units.form', [
                        'unit' => null,
                        'button' => 'Simpan',
                    ])
                </form>
            </div>
        </div>
    </div>
@endsection
