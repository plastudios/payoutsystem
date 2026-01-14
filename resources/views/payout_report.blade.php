@extends('layouts.app')

@section('title', 'Payout Report')

@section('content')
<h2 class="mb-4">Payout Report</h2>

<form action="{{ url('/payout/report') }}" method="POST" class="row g-3 mb-4">
    @csrf
    <div class="col-md-4">
        <label>From Date</label>
        <input type="date" name="from_date" value="{{ old('from_date', $from ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label>To Date</label>
        <input type="date" name="to_date" value="{{ old('to_date', $to ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-4 align-self-end">
        <button class="btn btn-primary" type="submit">Search</button>
    </div>
</form>

@if(isset($payouts))
    <table class="table table-bordered" id="payoutReportTable">
        <thead class="table-dark">
            <tr>
                <th>Batch ID</th>
                <th>Ref Key</th>
                <th>Merchant ID</th>
                <th>Bank Code</th>
                <th>Account Name</th>
                <th>Account Number</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @php
                $successAmount = 0;
                $failedAmount = 0;
            @endphp
            @foreach($payouts as $payout)
                @php
                    if ($payout->status === 'Success') $successAmount += $payout->amount;
                    if ($payout->status === 'Failed') $failedAmount += $payout->amount;
                @endphp
                <tr>
                    <td>{{ $payout->batch_id }}</td>
                    <td>{{ $payout->referenceKey }}</td>
                    <td>{{ $payout->merchant_id }}</td>
                    <td>{{ $payout->bankCode }}</td>
                    <td>{{ $payout->beneficiaryName }}</td>
                    <td>{{ $payout->beneficiaryAcc }}</td>
                    <td>{{ number_format($payout->amount, 2) }} BDT</td>
                    <td>
                        <span class="badge bg-{{ $payout->status === 'Success' ? 'success' : 'danger' }}">
                            {{ $payout->status }}
                        </span>
                    </td>
                    <td>{{ $payout->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        <h5>Summary:</h5>
        <ul>
            <li><strong>Total Success Amount:</strong> {{ number_format($successAmount, 2) }} BDT</li>
            <li><strong>Total Failed Amount:</strong> {{ number_format($failedAmount, 2) }} BDT</li>
            <li><strong>Total Processed Amount:</strong> {{ number_format($successAmount + $failedAmount, 2) }} BDT</li>
        </ul>
    </div>
@endif
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" />
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        $(document).ready(function () {
            $('#payoutReportTable').DataTable({
                dom: 'Bfrtip',
                buttons: ['copy', 'excel', 'pdf', 'print'],
                paging: false
            });
        });
    </script>
@endsection
