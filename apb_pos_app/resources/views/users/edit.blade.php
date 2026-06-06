@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Edit User</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('users.update', $id) }}" method="POST">
                @csrf
                @method('PUT')

                @include('users.form', [
                    'user' => $user,
                    'button' => 'Update',
                ])
            </form>
        </div>
    </div>
@endsection
