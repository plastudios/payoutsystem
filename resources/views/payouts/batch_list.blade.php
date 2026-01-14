@extends('layouts.app')
@section('title', 'Payout Batches')

@section('content')
<h2>Payout Batches</h2>
<table id="batchTable" class="table table-bordered">
    <thead>
        <tr>
            <th>Batch ID</th>
            <th>Merchant ID</th>
            <th>Total Amount</th>
            <th>Total Count</th>
            <th>Pending</th>
            <th>Success</th>
            <th>Failed</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($batches as $batch)
        <tr>
            <td 
                @if ($batch->pending_amount > 0)
                    style="color: red; font-weight: bold;"  {{-- Color the Batch ID red if there is a pending amount --}}
                @endif
            >
                {{ $batch->batch_id }}
            </td>
            <td>{{ $batch->merchant_id }}</td>
            <td>{{ number_format($batch->total_amount, 2) }} BDT</td>
            <td>{{ $batch->total_count }}</td>
            <td class="text-warning">{{ number_format($batch->pending_amount, 2) }}</td>
            <td class="text-success">{{ number_format($batch->success_amount, 2) }}</td>
            <td class="text-danger">{{ number_format($batch->failed_amount, 2) }}</td>
            <td>
                <a href="{{ route('payout.batch.details', $batch->batch_id) }}" class="btn btn-sm btn-info">View Details</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#batchTable').DataTable();
    });
</script>
@endsection
