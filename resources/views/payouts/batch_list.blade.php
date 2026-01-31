@extends('layouts.app')
@section('title', 'Payout Batches')

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

    .table-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
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

    .batch-id-pending {
        color: #dc2626;
        font-weight: 700;
    }

    .batch-id-normal {
        font-weight: 600;
        color: #1e293b;
    }

    .amount-display {
        font-weight: 700;
    }

    .btn-info {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
        font-weight: 600;
    }

    .btn-info:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
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
        .table-card {
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
        <h2><i class="fas fa-layer-group me-3"></i>Payout Batches</h2>
        <p class="mb-0 opacity-90">Overview of all payout batches and their processing status</p>
    </div>

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-responsive">
            <table id="batchTable" class="table table-hover">
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
                        <td>
                            <span class="{{ $batch->pending_amount > 0 ? 'batch-id-pending' : 'batch-id-normal' }}">
                                {{ $batch->batch_id }}
                                @if ($batch->pending_amount > 0)
                                    <i class="fas fa-exclamation-circle ms-1" title="Has pending amount"></i>
                                @endif
                            </span>
                        </td>
                        <td>{{ $batch->merchant_id }}</td>
                        <td>
                            <span class="amount-display">{{ number_format($batch->total_amount, 2) }}</span>
                            <small class="text-muted">BDT</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $batch->total_count }}</span></td>
                        <td class="text-warning"><strong>{{ number_format($batch->pending_amount, 2) }}</strong></td>
                        <td class="text-success"><strong>{{ number_format($batch->success_amount, 2) }}</strong></td>
                        <td class="text-danger"><strong>{{ number_format($batch->failed_amount, 2) }}</strong></td>
                        <td>
                            <a href="{{ route('payout.batch.details', $batch->batch_id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye me-1"></i> View Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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
    $(document).ready(function() {
        $('#batchTable').DataTable({
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
    });
</script>
@endsection
