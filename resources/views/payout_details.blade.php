@extends('layouts.app')

@section('title', 'Payout Details')

@section('content')
<h2 class="mb-4">Payout Details</h2>

<table class="table table-bordered table-striped" id="payoutTable">
    <thead>
        <tr>
            <th>Batch ID</th>
            <th>Merchant ID</th>
            <th>Ref. Key</th>
            <th>Amount</th>
            <th>Beneficiary</th>
            <th>Bank</th>
            <th>Account</th>
            <th>Status</th>
            <th>API Response</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payouts as $payout)
        <tr>
            <td>{{ $payout->batch_id }}</td>
            <td>{{ $payout->merchant_id }}</td>
            <td>{{ $payout->referenceKey }}</td>
            <td>{{ $payout->amount }} {{ $payout->currency }}</td>
            <td>{{ $payout->beneficiaryName }}</td>
            <td>{{ $payout->bankShortCode }}</td>
            <td>{{ $payout->beneficiaryAcc }}</td>
            <td>
                <span class="badge bg-{{ $payout->status == 'Success' ? 'success' : ($payout->status == 'Failed' ? 'danger' : 'secondary') }}">
                    {{ $payout->status }}
                </span>
            </td>
            @php
                $response = json_decode($payout->api_response, true);
            @endphp
            <td><pre class="mb-0">{{ json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre></td>
            <td>{{ $payout->created_at->format('Y-m-d H:i') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>


@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#payoutTable').DataTable({
            dom: 'Bfrtip',
            buttons: ['copy', 'excel', 'pdf', 'print']
        });
    });
</script>
@endsection
