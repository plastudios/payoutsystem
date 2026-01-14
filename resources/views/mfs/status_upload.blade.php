@extends('layouts.app')

@section('title', 'Update MFS Payout Status')

@section('content')
<div class="container">
    <h2 class="mb-4">🔁 Upload Processed Status File</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('mfs.status.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="status_file" class="form-label">Select Status Excel</label>
            <input type="file" class="form-control" name="status_file" required accept=".xlsx,.xls">
        </div>
        <button class="btn btn-success">Update Status</button>
    </form>
</div>
@endsection
