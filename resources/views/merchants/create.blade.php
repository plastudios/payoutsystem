@extends('layouts.app')
@section('content')
<h2>Add Merchant</h2>
<form action="{{ url('/merchants') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Merchant ID</label>
        <input type="text" name="merchant_id" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Company Name</label>
        <input type="text" name="company_name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Status</label>
        <select name="status" class="form-control" required>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Save Merchant</button>
</form>
@endsection
