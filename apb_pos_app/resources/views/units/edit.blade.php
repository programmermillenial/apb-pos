@extends('layouts.app')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Edit Unit</h4>
            </div>

            <div class="card-body">
                <form action="{{ route('units.update', $id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('units.form', [
                        'unit' => $unit,
                        'button' => 'Update',
                    ])
                </form>
            </div>
        </div>
    </div>
@endsection
