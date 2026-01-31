@extends('layouts.app')
@section('title', 'Export MFS Payout Batches')

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

    .page-header p {
        margin: 8px 0 0 0;
        opacity: 0.9;
        font-size: 0.9rem;
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
        color: #10b981;
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

    .text-warning {
        color: #f59e0b !important;
        font-weight: 600;
    }

    .text-success {
        color: #10b981 !important;
        font-weight: 600;
    }

    .text-danger {
        color: #ef4444 !important;
        font-weight: 600;
    }

    .btn-export {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.8rem;
        transition: all 0.2s ease;
    }

    .btn-export:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
        color: white;
    }

    .btn-export i {
        margin-right: 6px;
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

        .btn-export {
            padding: 5px 10px;
            font-size: 0.7rem;
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
        <h2><i class="fas fa-file-export me-3"></i>Export MFS Payout Batches</h2>
        <p>Download processed MFS payout batches with status breakdowns</p>
    </div>

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-header">
            <i class="fas fa-table"></i>
            <h5>Available Batches for Export</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="batchesTable">
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
                            <td><strong>{{ $batch->batch_id }}</strong></td>
                            <td><code>{{ $batch->merchant_id }}</code></td>
                            <td><strong>{{ number_format($batch->total_amount, 2) }}</strong> <small class="text-muted">BDT</small></td>
                            <td><span class="badge bg-secondary">{{ $batch->count }}</span></td>
                            <td class="text-warning">{{ number_format($batch->pending, 2) }}</td>
                            <td class="text-success">{{ number_format($batch->success, 2) }}</td>
                            <td class="text-danger">{{ number_format($batch->failed, 2) }}</td>
                            <td>
                                <a href="{{ route('mfs.export', $batch->batch_id) }}" class="btn btn-export btn-sm">
                                    <i class="fas fa-download"></i> Export
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
    $(document).ready(function () {
        $('#batchesTable').DataTable({
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
