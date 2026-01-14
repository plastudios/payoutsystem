@extends('layouts.app')

@section('title', 'Payouts to Approve')

@section('content')
<div class="container">
    <h2 class="mb-4">✅ Payouts Checked and Ready for Maker Approval</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>Batch ID</th>
                <th>Merchant</th>
                <th>Total Amount</th>
                <th>Row Count</th>
                <th>Status</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payouts as $payout)
                <tr>
                    <td>{{ $payout->batch_id }}</td>
                    <td>{{ $payout->merchant_id }}</td>
                    <td>{{ number_format($payout->amount, 2) }} BDT</td>
                    <td>{{ $payout->count }}</td>
                    <td><span class="badge bg-info text-dark">Checked</span></td>
                    <td>
                        <div class="d-flex gap-2 justify-content-center">
                            <form action="{{ route('payouts.approve_process', $payout->batch_id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    ✅ Approve
                                </button>
                            </form>

                            <form action="{{ route('payout.reject', $payout->batch_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this batch?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    ❌ Reject
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No checked payouts found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<style>
    .d-flex {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
</style>
@endsection
