@extends('layouts.app')
@section('title', 'Edit Agent')
@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="dashboard-card">
            <h2 class="mb-4">Edit Agent</h2>

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

            <form action="{{ route('agents.update', $agent->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $agent->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $agent->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $agent->phone) }}" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password <span class="text-muted">(leave blank to keep current)</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Merchants <span class="text-danger">*</span></label>
                    <select name="merchant_ids[]" class="form-control @error('merchant_ids') is-invalid @enderror" multiple size="8" required>
                        @php
                            $selectedIds = old('merchant_ids', $agent->merchants->pluck('merchant_id')->toArray());
                        @endphp
                        @foreach($merchants as $merchant)
                            <option value="{{ $merchant->merchant_id }}" {{ in_array($merchant->merchant_id, $selectedIds) ? 'selected' : '' }}>
                                {{ $merchant->merchant_id }} - {{ $merchant->company_name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple merchants.</small>
                    @error('merchant_ids')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Agent</button>
                    <a href="{{ route('agents.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
