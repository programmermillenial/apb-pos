@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Tambah User</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                @include('users.form', [
                    'user' => null,
                    'button' => 'Simpan',
                ])
            </form>
        </div>
    </div>
@endsection
