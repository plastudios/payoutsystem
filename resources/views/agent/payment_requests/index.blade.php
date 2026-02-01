@extends('layouts.app')

@section('title', 'Payment Request')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
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
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(30px, -30px);
    }

    .page-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.75rem;
        position: relative;
        z-index: 2;
    }

    .page-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
        font-size: 0.95rem;
        position: relative;
        z-index: 2;
    }

    .filter-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .filter-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    .filter-header i {
        color: #3b82f6;
        margin-right: 8px;
        font-size: 1.1rem;
    }

    .filter-header h5 {
        margin: 0;
        font-weight: 600;
        color: #1e293b;
        font-size: 1.1rem;
    }

    .filter-form .form-label {
        font-weight: 600;
        color: #374151;
        font-size: 0.875rem;
        margin-bottom: 8px;
    }

    .filter-form .form-control,
    .filter-form .form-select {
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 0.875rem;
        background: #ffffff;
    }

    .filter-form .form-control:focus,
    .filter-form .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .filter-form .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: none;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 8px;
    }

    .filter-form .btn-outline-secondary {
        border: 1.5px solid #e2e8f0;
        color: #6b7280;
        background: white;
    }

    .table-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .table-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    .table-header i {
        color: #3b82f6;
        margin-right: 8px;
        font-size: 1.1rem;
    }

    .table-header h5 {
        margin: 0;
        font-weight: 600;
        color: #1e293b;
        font-size: 1.1rem;
    }

    .dataTables_wrapper {
        font-family: 'Inter', sans-serif;
    }

    .dataTables_length select,
    .dataTables_filter input {
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 0.875rem;
    }

    .dataTables_filter input:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .dataTables_info {
        color: #6b7280;
        font-size: 0.875rem;
    }

    .dataTables_paginate .paginate_button {
        padding: 8px 12px !important;
        margin: 0 2px !important;
        border-radius: 6px !important;
        border: 1px solid #e2e8f0 !important;
        background: white !important;
        color: #374151 !important;
        font-size: 0.875rem !important;
    }

    .dataTables_paginate .paginate_button:hover {
        background: #f8fafc !important;
        border-color: #d1d5db !important;
    }

    .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        border-color: #3b82f6 !important;
        color: white !important;
    }

    .table {
        margin: 0;
        font-size: 0.875rem;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead th {
        background: #f8fafc;
        border: none;
        border-bottom: 2px solid #e2e8f0;
        color: #374151;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px;
    }

    .table tbody td {
        border: none;
        border-bottom: 1px solid #f1f5f9;
        padding: 12px;
        vertical-align: middle;
        color: #1e293b;
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

    .badge.bg-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    }

    .badge.bg-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
    }

    .badge.bg-secondary {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%) !important;
    }

    .action-buttons {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.75rem;
        border-radius: 6px;
    }

    .btn-outline-success {
        border: 1.5px solid #d1fae5;
        color: #059669;
        background: white;
    }

    .btn-outline-success:hover {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #047857;
    }

    .btn-outline-danger {
        border: 1.5px solid #fecaca;
        color: #dc2626;
        background: white;
    }

    .btn-outline-danger:hover {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #b91c1c;
    }

    .form-control.form-control-sm {
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 0.875rem;
    }

    .form-control.form-control-sm:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .alert {
        border-radius: 12px;
        border: none;
    }

    /* Mobile: filter form stack and full-width buttons */
    @media (max-width: 768px) {
        .page-header {
            padding: 16px;
            margin-bottom: 16px;
        }
        .page-header h2 {
            font-size: 1.5rem;
        }
        .page-header p {
            font-size: 0.875rem;
        }
        .filter-card {
            padding: 16px;
            margin-bottom: 16px;
        }
        .filter-form .col-md-3,
        .filter-form .col-md-2 {
            width: 100%;
            margin-bottom: 0;
        }
        .filter-form .d-flex.align-items-end {
            flex-direction: row;
            flex-wrap: wrap;
            gap: 8px !important;
            margin-top: 4px;
        }
        .filter-form .btn {
            flex: 1;
            min-width: 120px;
        }
        .table-card {
            padding: 16px;
            margin-bottom: 16px;
        }
        .table-header h5 {
            font-size: 1rem;
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0 -16px;
            padding: 0 16px;
        }
        .table {
            font-size: 0.8rem;
            min-width: 700px;
        }
        .table thead th {
            padding: 8px 6px;
            font-size: 0.65rem;
        }
        .table tbody td {
            padding: 8px 6px;
        }
        .action-buttons {
            flex-direction: row;
            flex-wrap: wrap;
            gap: 4px;
        }
        .action-buttons .btn-sm {
            padding: 6px 10px;
            font-size: 0.7rem;
        }
        .badge {
            font-size: 0.65rem;
            padding: 4px 8px;
        }
        .form-control.form-control-sm.mfs-trans-input {
            min-width: 90px;
            font-size: 0.8rem;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 10px;
        }
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            font-size: 0.8rem;
            padding: 6px 8px;
        }
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 0.75rem;
        }
        .dataTables_paginate .paginate_button {
            padding: 6px 10px !important;
            font-size: 0.75rem !important;
        }
    }

    /* Small phones */
    @media (max-width: 576px) {
        .page-header {
            padding: 12px;
        }
        .page-header h2 {
            font-size: 1.25rem;
        }
        .page-header h2 .me-3 {
            margin-right: 0.5rem !important;
        }
        .page-header p {
            font-size: 0.8rem;
        }
        .filter-card,
        .table-card {
            padding: 12px;
        }
        .filter-form .d-flex.align-items-end {
            flex-direction: column;
        }
        .filter-form .btn {
            width: 100%;
            min-width: 0;
        }
        .table-responsive {
            margin: 0 -12px;
            padding: 0 12px;
        }
        .table {
            font-size: 0.75rem;
            min-width: 600px;
        }
        .table thead th {
            padding: 6px 4px;
            font-size: 0.6rem;
        }
        .table tbody td {
            padding: 6px 4px;
        }
        .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h2><i class="fas fa-hand-holding-usd me-3"></i>Payment Request</h2>
    <p>Mark payouts as Success or Failed for your assigned merchants</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0 list-unstyled">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@php
    $f = $filter ?? ['start_date' => '', 'end_date' => '', 'status' => 'all'];
@endphp

<!-- Filter Card -->
<div class="filter-card">
    <div class="filter-header">
        <i class="fas fa-filter"></i>
        <h5>Filter Payouts</h5>
    </div>
    <form method="GET" class="row g-3 filter-form">
        <div class="col-12 col-md-3">
            <label class="form-label">From Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ $f['start_date'] }}">
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label">To Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ $f['end_date'] }}">
        </div>
        <div class="col-12 col-md-2">
            <label class="form-label">Status</label>
            @php $sel = $f['status'] ?? 'all'; @endphp
            <select name="status" class="form-select">
                <option value="all" {{ $sel === 'all' ? 'selected' : '' }}>All Status</option>
                <option value="Pending" {{ $sel === 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Success" {{ $sel === 'Success' ? 'selected' : '' }}>Success</option>
                <option value="Failed" {{ $sel === 'Failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex align-items-end gap-2 flex-wrap">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search me-1"></i> Apply
            </button>
            <a href="{{ route('agent.payment_requests.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-redo me-1"></i> Reset
            </a>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="table-card">
    <div class="table-header">
        <i class="fas fa-table"></i>
        <h5>Payment Requests</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover" id="paymentRequestTable">
            <thead>
                <tr>
                    <th>Wallet</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Request time</th>
                    <th>Completed time</th>
                    <th>MFS Trans ID</th>
                    <th>Remarks</th>
                    <th style="width:140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payouts as $payout)
                <tr>
                    <td>{{ $payout->wallet_number }}</td>
                    <td><span class="badge bg-light text-dark">{{ $payout->method }}</span></td>
                    <td><strong>{{ number_format($payout->amount, 2) }}</strong> <small class="text-muted">BDT</small></td>
                    <td>
                        <span class="badge bg-{{ $payout->status == 'Success' ? 'success' : ($payout->status == 'Failed' ? 'danger' : 'secondary') }}">
                            {{ $payout->status }}
                        </span>
                    </td>
                    <td>
                        <div>{{ $payout->created_at->format('M d, Y') }}</div>
                        <small class="text-muted">{{ $payout->created_at->format('H:i') }}</small>
                    </td>
                    <td>
                        @if($payout->completed_at)
                            <div>{{ $payout->completed_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $payout->completed_at->format('H:i') }}</small>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if($payout->status === 'Pending' && $payout->mfs_transaction_id === null)
                            <input type="text" class="form-control form-control-sm mfs-trans-input" name="mfs_transaction_id" placeholder="Enter MFS Trans ID" form="success-form-{{ $payout->id }}" required>
                        @else
                            {{ $payout->mfs_transaction_id ?? '—' }}
                        @endif
                    </td>
                    <td>{{ Str::limit($payout->remarks, 30) ?: '—' }}</td>
                    <td>
                        @if($payout->status === 'Pending')
                            <div class="action-buttons">
                                <form id="success-form-{{ $payout->id }}" action="{{ route('agent.payment_requests.success', $payout->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @if($payout->mfs_transaction_id !== null)
                                        <input type="hidden" name="mfs_transaction_id" value="{{ $payout->mfs_transaction_id }}">
                                    @endif
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Mark as Success">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Mark as Failed" data-bs-toggle="modal" data-bs-target="#failModal{{ $payout->id }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            {{-- Fail modal per row --}}
                            <div class="modal fade" id="failModal{{ $payout->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Mark as Failed</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('agent.payment_requests.fail', $payout->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Remark <span class="text-danger">*</span></label>
                                                    <textarea name="remark" class="form-control @error('remark') is-invalid @enderror" rows="3" required placeholder="Reason for failure"></textarea>
                                                    @error('remark')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Mark as Failed</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            —
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">No payment requests found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(function () {
    @if($payouts->isNotEmpty())
    $('#paymentRequestTable').DataTable({
        pageLength: 25,
        order: [[4, 'desc']],
        responsive: true,
        columnDefs: [
            { targets: -1, orderable: false }
        ],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ per page",
            info: "Showing _START_ to _END_ of _TOTAL_",
            infoEmpty: "No payment requests found",
            infoFiltered: "(filtered from _MAX_ total)"
        }
    });
    @endif
});
</script>
@endsection
