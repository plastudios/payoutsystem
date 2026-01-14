@extends('layouts.app')
@section('content')
<h2>Merchants List</h2>
<a href="{{ url('/merchants/create') }}" class="btn btn-success mb-3">Add Merchant</a>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Merchant ID</th>
            <th>Email</th>
            <th>Name</th>
            <th>Company</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($merchants as $merchant)
        <tr>
            <td>{{ $merchant->merchant_id }}</td>
            <td>{{ $merchant->email }}</td>
            <td>{{ $merchant->name }}</td>
            <td>{{ $merchant->company_name }}</td>
            <td>{{ ucfirst($merchant->status) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
