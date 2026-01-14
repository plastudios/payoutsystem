@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create New Merchant User</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.user.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="merchant_id">Merchant</label>
            <select name="merchant_id" class="form-control" required>
                <option value="">Select Merchant</option>
                @foreach($merchants as $merchant)
                    <option value="{{ $merchant->merchant_id }}">{{ $merchant->merchant_id }} - {{ $merchant->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="email">Merchant Email</label>
            <select name="email" class="form-control" required>
                <option value="">Select Email</option>
                @foreach($merchants as $merchant)
                    <option value="{{ $merchant->email }}">{{ $merchant->email }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="name">Merchant Name</label>
            <select name="name" class="form-control" required>
                <option value="">Select Name</option>
                @foreach($merchants as $merchant)
                    <option value="{{ $merchant->name }}">{{ $merchant->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input name="password" type="password" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input name="password_confirmation" type="password" class="form-control" required>
        </div>

        <button class="btn btn-primary">Create User</button>
    </form>
</div>
@endsection
