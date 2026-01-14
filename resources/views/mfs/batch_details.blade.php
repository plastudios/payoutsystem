@extends('layouts.app')
@section('title', "MFS Batch: $batchId")

@section('content')
<div class="container">
    <h3 class="mb-4">📄 MFS Payouts for Batch: <strong>{{ $batchId }}</strong></h3>
    <a href="{{ route('mfs.batches') }}" class="btn btn-sm btn-primary mb-3">← Back to Batches</a>

    <table class="table table-bordered" id="batchPayoutTable">
        <thead>
            <tr>
                <th>Reference</th>
                <th>Merchant ID</th>
                <th>Amount</th>
                <th>Wallet</th>
                <th>Method</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payouts as $payout)
                <tr>
                    <td>{{ $payout->reference_key }}</td>
                    <td>{{ $payout->merchant_id }}</td>
                    <td>{{ number_format($payout->amount, 2) }} BDT</td>
                    <td>{{ $payout->wallet_number }}</td>
                    <td>{{ ucfirst($payout->method) }}</td>
                    <td>
                        @if($payout->status === 'Success')
                            <span class="badge bg-success">{{ $payout->status }}</span>
                        @elseif($payout->status === 'Failed')
                            <span class="badge bg-danger">{{ $payout->status }}</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ $payout->status }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#batchPayoutTable').DataTable({
            dom: 'Bfrtip',
            buttons: ['copy', 'excel', 'pdf', 'print']
        });
    });
</script>
@endsection
