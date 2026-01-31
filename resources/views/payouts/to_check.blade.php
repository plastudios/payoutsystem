@extends('layouts.app')

@section('title', 'Payouts to Check')

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

    .alert {
        border-radius: 12px;
        border: none;
        padding: 16px 20px;
        margin-bottom: 24px;
        font-weight: 500;
    }

    .alert-success {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
    }

    .alert-danger {
        background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
        color: #dc2626;
    }

    .table-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .table-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    .table-header i {
        color: #f59e0b;
        margin-right: 8px;
        font-size: 1.1rem;
    }

    .table-header h5 {
        margin: 0;
        font-weight: 600;
        color: #1e293b;
        font-size: 1.1rem;
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

    .badge.bg-warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%) !important;
        color: #92400e !important;
        border: 1px solid #f59e0b;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-approve {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .btn-approve:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
        color: white;
    }

    .btn-reject {
        background: white;
        color: #dc2626;
        border: 1.5px solid #fca5a5;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .btn-reject:hover {
        background: #fef2f2;
        border-color: #ef4444;
        color: #b91c1c;
        transform: translateY(-1px);
    }

    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: #6b7280;
    }

    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 16px;
    }

    .empty-state h5 {
        color: #374151;
        font-weight: 600;
        margin-bottom: 8px;
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

    /* Responsive */
    @media (max-width: 992px) {
        .page-header p {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 768px) {
        .table-card {
            padding: 16px;
        }
        
        .page-header {
            padding: 16px;
        }
        
        .page-header h2 {
            font-size: 1.5rem;
        }

        .page-header p {
            font-size: 0.75rem;
        }

        .table-header h5 {
            font-size: 1rem;
        }

        .alert {
            padding: 12px 16px;
            font-size: 0.85rem;
        }

        .table {
            font-size: 0.75rem;
        }

        .table thead th {
            font-size: 0.7rem;
            padding: 8px;
        }

        .table tbody td {
            padding: 8px;
        }

        .badge {
            font-size: 0.7rem;
            padding: 4px 8px;
        }

        .action-buttons {
            flex-direction: column;
            gap: 4px;
        }

        .btn-approve,
        .btn-reject {
            padding: 6px 12px;
            font-size: 0.75rem;
            width: 100%;
        }

        .empty-state {
            padding: 32px 16px;
        }

        .empty-state i {
            font-size: 2rem;
        }

        /* DataTables mobile improvements */
        .dt-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-bottom: 12px;
        }

        .dt-button {
            padding: 6px 12px !important;
            font-size: 0.75rem !important;
            margin-right: 0 !important;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 12px;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 0.75rem;
        }

        .dataTables_length select,
        .dataTables_filter input {
            font-size: 0.75rem;
            padding: 4px 8px;
        }
    }

    @media (max-width: 576px) {
        .page-header h2 {
            font-size: 1.25rem;
        }

        .table-header i {
            font-size: 0.9rem;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h2><i class="fas fa-clipboard-check me-3"></i>Payouts Pending Checker Review</h2>
        <p class="mb-0 opacity-90">Review and approve or reject payout batches pending checker verification</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-header">
            <i class="fas fa-table"></i>
            <h5>Pending Payout Batches</h5>
        </div>

        @if(count($payouts) > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="payoutsTable">
                    <thead>
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
                        @foreach($payouts as $payout)
                            <tr>
                                <td><strong>{{ $payout->batch_id }}</strong></td>
                                <td><code>{{ $payout->merchant_id }}</code></td>
                                <td><strong>{{ number_format($payout->amount, 2) }}</strong> <small class="text-muted">BDT</small></td>
                                <td><span class="badge bg-secondary">{{ $payout->total_rows }}</span></td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <form action="{{ route('payouts.mark_checked', $payout->batch_id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-approve btn-sm">
                                                <i class="fas fa-check me-1"></i> Approve
                                            </button>
                                        </form>

                                        <form action="{{ route('payout.reject', $payout->batch_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this batch?')">
                                            @csrf
                                            <button type="submit" class="btn btn-reject btn-sm">
                                                <i class="fas fa-times me-1"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h5>No Pending Batches</h5>
                <p class="mb-0">There are no payout batches pending checker review at the moment.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
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
        @if(count($payouts) > 0)
        $('#payoutsTable').DataTable({
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
            pageLength: 25,
            responsive: true,
            order: [[0, 'desc']],
            language: {
                search: "Search batches:",
                lengthMenu: "Show _MENU_ batches per page",
                info: "Showing _START_ to _END_ of _TOTAL_ batches",
                infoEmpty: "No batches found",
                infoFiltered: "(filtered from _MAX_ total batches)"
            }
        });
        @endif
    });
</script>
@endsection
