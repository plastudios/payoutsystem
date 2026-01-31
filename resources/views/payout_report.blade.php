@extends('layouts.app')

@section('title', 'Payout Report')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<style>
    body {
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
    }

    .page-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .page-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.75rem;
    }

    .filter-card, .table-card, .summary-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .form-label {
        font-weight: 600;
        color: #374151;
        font-size: 0.875rem;
        margin-bottom: 8px;
    }

    .form-control {
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 0.875rem;
    }

    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn {
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 0.875rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
    }

    .table {
        margin: 0;
        font-size: 0.875rem;
    }

    .table thead th {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        color: #374151;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        padding: 12px;
    }

    .table tbody td {
        border-bottom: 1px solid #f1f5f9;
        padding: 12px;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background: #f8fafc;
    }

    .badge {
        font-weight: 600;
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 20px;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .summary-item {
        background: #f8fafc;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .summary-label {
        color: #64748b;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .summary-value {
        color: #1e293b;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .dt-button {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important;
        border: 1px solid #d1d5db !important;
        color: #374151 !important;
        padding: 8px 16px !important;
        border-radius: 6px !important;
        font-size: 0.875rem !important;
        font-weight: 500 !important;
        margin-right: 8px !important;
    }

    .dt-button:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    }

    @media (max-width: 768px) {
        .filter-card, .table-card, .summary-card {
            padding: 16px;
        }
        
        .page-header {
            padding: 16px;
        }
        
        .page-header h2 {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h2><i class="fas fa-chart-line me-3"></i>Payout Report</h2>
        <p class="mb-0 opacity-90">Search and analyze payout transactions by date range</p>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <form action="{{ url('/payout/report') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-5">
                <label class="form-label">From Date</label>
                <input type="date" name="from_date" value="{{ old('from_date', $from ?? '') }}" class="form-control" required>
            </div>
            <div class="col-md-5">
                <label class="form-label">To Date</label>
                <input type="date" name="to_date" value="{{ old('to_date', $to ?? '') }}" class="form-control" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </div>
        </form>
    </div>

    @if(isset($payouts))
        @php
            $successAmount = 0;
            $failedAmount = 0;
            foreach($payouts as $payout) {
                if ($payout->status === 'Success') $successAmount += $payout->amount;
                if ($payout->status === 'Failed') $failedAmount += $payout->amount;
            }
        @endphp

        <!-- Summary Card -->
        <div class="summary-card">
            <h5 class="mb-4"><i class="fas fa-chart-pie me-2"></i>Summary</h5>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Total Success Amount</div>
                    <div class="summary-value text-success">{{ number_format($successAmount, 2) }} <small>BDT</small></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Failed Amount</div>
                    <div class="summary-value text-danger">{{ number_format($failedAmount, 2) }} <small>BDT</small></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Processed Amount</div>
                    <div class="summary-value text-primary">{{ number_format($successAmount + $failedAmount, 2) }} <small>BDT</small></div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover" id="payoutReportTable">
                    <thead>
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
                        @foreach($payouts as $payout)
                            <tr>
                                <td><strong>{{ $payout->batch_id }}</strong></td>
                                <td><code>{{ $payout->referenceKey }}</code></td>
                                <td>{{ $payout->merchant_id }}</td>
                                <td>{{ $payout->bankCode }}</td>
                                <td>{{ $payout->beneficiaryName }}</td>
                                <td>{{ $payout->beneficiaryAcc }}</td>
                                <td><strong>{{ number_format($payout->amount, 2) }}</strong> <small class="text-muted">BDT</small></td>
                                <td>
                                    <span class="badge bg-{{ $payout->status === 'Success' ? 'success' : 'danger' }}">
                                        {{ $payout->status }}
                                    </span>
                                </td>
                                <td>{{ $payout->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<invoke name="cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
    $(document).ready(function () {
        $('#payoutReportTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="fas fa-copy me-1"></i> Copy',
                    className: 'dt-button'
                },
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel me-1"></i> Excel',
                    className: 'dt-button'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                    className: 'dt-button'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print me-1"></i> Print',
                    className: 'dt-button'
                }
            ],
            pageLength: 50,
            responsive: true,
            order: [[8, 'desc']],
            language: {
                search: "Search payouts:",
                lengthMenu: "Show _MENU_ payouts per page",
                info: "Showing _START_ to _END_ of _TOTAL_ payouts",
                infoEmpty: "No payouts found",
                infoFiltered: "(filtered from _MAX_ total payouts)"
            }
        });
    });
</script>
@endsection
