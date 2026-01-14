@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
<h2>Edit User</h2>
<form action="{{ route('users.update', ['id' => $user->id]) }}" method="POST">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Merchant</label>
        <select name="merchant_id" class="form-control" required>
            @foreach($merchants as $merchant)
                <option value="{{ $merchant->merchant_id }}" {{ $user->merchant_id == $merchant->merchant_id ? 'selected' : '' }}>
                    {{ $merchant->merchant_id }} - {{ $merchant->company_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
    </div>
    <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-control" required>
            @foreach(['admin', 'author', 'checker', 'maker', 'merchant'] as $role)
                <option value="{{ $role }}" {{ $user->role == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-primary">Update</button>
</form>
@endsection