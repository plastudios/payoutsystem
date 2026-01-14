@extends('layouts.app')

@section('title', 'Merchant Balance Management')

@section('content')
    <h2>💰 Manage Merchant Balance</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('merchant.balance.store') }}" method="POST" class="row g-3 mb-4">
        @csrf
        <div class="col-md-3">
            <label class="form-label">Merchant</label>
            <select name="merchant_id" class="form-control" required>
                <option value="">-- Select Merchant --</option>
                @foreach($merchants as $merchant)
                    <option value="{{ $merchant->merchant_id }}">{{ $merchant->merchant_id }} ({{ $merchant->company_name }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Type</label>
            <select name="type" class="form-control" required>
                <option value="credit">Credit</option>
                <option value="debit">Debit</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Amount</label>
            <input type="number" name="amount" step="0.01" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Remarks</label>
            <input type="text" name="remarks" class="form-control">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Submit</button>
        </div>
    </form>

    <h4>📊 Merchant Summary</h4>

    <table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Merchant ID</th>
            <th>Company Name</th>
            <th>Total Credit</th>
            <th>Total Debit</th>
            <th>Available Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach($summary as $row)
            <tr>
                <td>{{ $row['merchant_id'] }}</td>
                <td>{{ $row['company_name'] }}</td>
                <td>{{ number_format($row['credit'], 2) }}</td>
                <td class="text-warning">{{ number_format($row['debit'], 2) }}</td>
                <td class="text-success"><strong>{{ number_format($row['available'], 2) }}</strong></td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#summaryTable').DataTable();
        });
    </script>
@endpush

@push('styles')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush
