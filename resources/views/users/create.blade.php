@extends('layouts.app')
@section('title', 'Create User')
@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="dashboard-card">
            <h2 class="mb-4">Create New User</h2>
            
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

            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Merchant</label>
                    <select name="merchant_id" class="form-control @error('merchant_id') is-invalid @enderror" required>
                        <option value="">-- Select Merchant --</option>
                        @foreach($merchants as $merchant)
                            <option value="{{ $merchant->merchant_id }}" {{ old('merchant_id') == $merchant->merchant_id ? 'selected' : '' }}>
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
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="author" {{ old('role') == 'author' ? 'selected' : '' }}>Author</option>
                        <option value="checker" {{ old('role') == 'checker' ? 'selected' : '' }}>Checker</option>
                        <option value="maker" {{ old('role') == 'maker' ? 'selected' : '' }}>Maker</option>
                        <option value="merchant" {{ old('role') == 'merchant' ? 'selected' : '' }}>Merchant</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Create User</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection