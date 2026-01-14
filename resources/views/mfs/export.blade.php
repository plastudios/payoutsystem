@extends('layouts.app')
@section('title', 'Export MFS Payout Batches')

@section('content')
<div class="container">
    <h2 class="mb-4">📦 Export MFS Payout Batches</h2>

    <table class="table table-bordered">
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
            @foreach($batches as $batch)
                <tr>
                    <td>{{ $batch->batch_id }}</td>
                    <td>{{ $batch->merchant_id }}</td>
                    <td>{{ number_format($batch->total_amount, 2) }} BDT</td>
                    <td>{{ $batch->count }}</td>
                    <td class="text-warning">{{ number_format($batch->pending, 2) }}</td>
                    <td class="text-success">{{ number_format($batch->success, 2) }}</td>
                    <td class="text-danger">{{ number_format($batch->failed, 2) }}</td>
                    <td>
                    <a href="{{ route('mfs.export', $batch->batch_id) }}" class="btn btn-sm btn-info">
                        Export
                    </a>
                </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
