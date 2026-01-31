@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="dashboard-card">
            <h2 class="mb-4">Edit User</h2>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('users.update', ['id' => $user->id]) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Merchant</label>
                    <select name="merchant_id" class="form-control @error('merchant_id') is-invalid @enderror" required>
                        @foreach($merchants as $merchant)
                            <option value="{{ $merchant->merchant_id }}" {{ (old('merchant_id', $user->merchant_id) == $merchant->merchant_id) ? 'selected' : '' }}>
                                {{ $merchant->merchant_id }} - {{ $merchant->company_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('merchant_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                        @foreach(['admin', 'author', 'checker', 'maker', 'merchant'] as $role)
                            <option value="{{ $role }}" {{ (old('role', $user->role) == $role) ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update User</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection