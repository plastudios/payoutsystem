@extends('layouts.app')
@section('title', 'Batch: ' . $batchId)

@section('content')
<h2>Payouts for Batch: {{ $batchId }}  <button onclick="window.history.back()" class="btn btn-primary">← Back</button></h2>
<table id="detailTable" class="table table-striped">
    <thead>
        <tr>
            <th>Reference</th>
            <th>Merchant ID</th>
            <th>Amount</th>
            <th>Beneficiary</th>
            <th>Bank</th>
            <th>Account</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($payouts as $payout)
        <tr>
            <td>{{ $payout->referenceKey }}</td>
            <td>{{ $payout->merchant_id }}</td>
            <td>{{ number_format($payout->amount, 2) }} BDT</td>
            <td>{{ $payout->beneficiaryName }}</td>
            <td>{{ $payout->bankShortCode }}</td>
            <td>{{ $payout->beneficiaryAcc }}</td>
            <td>
                <span class="badge 
                    {{ $payout->status === 'Success' ? 'bg-success' : 
                       ($payout->status === 'Failed' ? 'bg-danger' : 'bg-warning') }}">
                    {{ $payout->status }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#detailTable').DataTable();
    });
</script>
@endsection
