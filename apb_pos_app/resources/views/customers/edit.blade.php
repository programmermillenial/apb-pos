@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Edit Customer</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('customers.update', $id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @include('customers.form', [
                            'customer' => $customer,
                            'button' => 'Update',
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
