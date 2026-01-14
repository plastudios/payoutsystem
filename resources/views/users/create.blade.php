@extends('layouts.app')
@section('title', 'Create User')
@section('content')
<h2>Create New User</h2>
<form action="{{ route('users.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Merchant</label>
        <select name="merchant_id" class="form-control" required>
            <option value="">-- Select Merchant --</option>
            @foreach($merchants as $merchant)
                <option value="{{ $merchant->merchant_id }}">{{ $merchant->merchant_id }} - {{ $merchant->company_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-control" required>
            <option value="admin">Admin</option>
            <option value="author">Author</option>
            <option value="checker">Checker</option>
            <option value="maker">Maker</option>
            <option value="merchant">Merchant</option>
        </select>
    </div>
    <button class="btn btn-success">Submit</button>
</form>
@endsection