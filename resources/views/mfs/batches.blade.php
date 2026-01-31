@extends('layouts.app')

@section('title', 'MFS Payout Batches')

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

    .page-subtitle {
        opacity: 0.9;
        font-size: 0.875rem;
        margin-top: 4px;
        position: relative;
        z-index: 2;
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stats-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--card-color, #f59e0b);
        border-radius: 12px 12px 0 0;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .stats-card .icon-wrapper {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        background: var(--card-color, #f59e0b);
        color: white;
        font-size: 1.1rem;
    }

    .stats-card .stats-label {
        color: #64748b;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }

    .stats-card .stats-value {
        color: #1e293b;
        font-weight: 700;
        font-size: 1.5rem;
        margin: 0;
        line-height: 1;
    }

    .stats-card.total-batches { --card-color: #f59e0b; }
    .stats-card.total-amount { --card-color: #3b82f6; }
    .stats-card.success-amount { --card-color: #10b981; }
    .stats-card.failed-amount { --card-color: #ef4444; }

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

    /* DataTables Styling */
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
        border-color: #f59e0b;
        outline: none;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
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
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        border-color: #f59e0b !important;
        color: white !important;
    }

    /* Table Styling */
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

    /* Status Badges */
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #f59e0b;
    }

    .status-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #10b981;
    }

    .status-failed {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #ef4444;
    }

    /* Buttons */
    .btn {
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        border: none;
    }

    .btn-view {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
    }

    .btn-view:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(245, 158, 11, 0.3);
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        color: white;
    }

    /* DataTables Buttons */
    .dt-buttons {
        margin-bottom: 16px;
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
        transition: all 0.2s ease !important;
    }

    .dt-button:hover {
        background: linear-gradient(135deg, #e2e8f0 0%, #d1d5db 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    }

    /* Modal Styling */
    .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .modal-header {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        border-radius: 16px 16px 0 0;
        border: none;
        padding: 20px 24px;
    }

    .modal-title {
        font-weight: 700;
        font-size: 1.25rem;
    }

    .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    .btn-close:hover {
        opacity: 1;
    }

    .modal-body {
        padding: 24px;
    }

    .batch-info {
        background: #f8fafc;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
    }

    .batch-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .batch-info-item {
        text-align: center;
    }

    .batch-info-label {
        color: #6b7280;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 4px;
    }

    .batch-info-value {
        color: #1e293b;
        font-weight: 700;
        font-size: 1.25rem;
    }

    /* Batch ID styling */
    .batch-id {
        font-family: 'Monaco', 'Consolas', monospace;
        background: #f1f5f9;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e293b;
    }

    /* Enhanced amount styling */
    .amount-display {
        font-weight: 700;
    }

    .amount-positive {
        color: #059669;
    }

    .amount-negative {
        color: #dc2626;
    }

    .amount-neutral {
        color: #6b7280;
    }

    /* Loading state */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        z-index: 10;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid #f1f5f9;
        border-top: 3px solid #f59e0b;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 992px) {
        .stats-cards {
            grid-template-columns: repeat(2, 1fr);
        }

        .page-subtitle {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 768px) {
        .table-card {
            padding: 16px;
            margin-bottom: 16px;
        }
        
        .page-header {
            padding: 16px;
        }
        
        .page-header h2 {
            font-size: 1.5rem;
        }

        .page-subtitle {
            font-size: 0.75rem;
        }

        .table-header h5 {
            font-size: 1rem;
        }
        
        .stats-cards {
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .stats-card {
            padding: 16px;
        }

        .stats-card .icon-wrapper {
            width: 40px;
            height: 40px;
            font-size: 1rem;
            margin-bottom: 12px;
        }

        .stats-card .stats-label {
            font-size: 0.75rem;
        }

        .stats-card .stats-value {
            font-size: 1.25rem;
        }

        .stats-card .stats-value small {
            font-size: 0.7rem !important;
        }
        
        .modal-dialog {
            margin: 10px;
        }
        
        .batch-info-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        /* Table improvements */
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

        .batch-id {
            font-size: 0.75rem;
            padding: 3px 6px;
        }

        .amount-display {
            font-size: 0.85rem;
        }

        .status-badge {
            font-size: 0.65rem;
            padding: 3px 8px;
        }

        .status-badge i {
            font-size: 0.6rem;
        }

        .badge {
            font-size: 0.7rem;
            padding: 4px 8px;
        }

        .btn-view {
            padding: 6px 12px;
            font-size: 0.75rem;
        }

        .btn-view i {
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

        /* Modal improvements */
        .modal-header {
            padding: 16px;
        }

        .modal-title {
            font-size: 1.1rem;
        }

        .modal-body {
            padding: 16px;
        }

        .batch-info {
            padding: 16px;
        }

        .batch-info-value {
            font-size: 1rem;
        }
    }

    @media (max-width: 576px) {
        .stats-cards {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .stats-card {
            padding: 16px;
        }

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

        .btn-view {
            padding: 4px 8px;
            font-size: 0.7rem;
        }

        .btn-view i {
            display: none;
        }

        /* Modal on very small screens */
        .modal-dialog {
            margin: 5px;
        }

        .batch-info-grid {
            gap: 8px;
        }

        .batch-info-item {
            padding: 8px;
        }
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-in {
        animation: fadeInUp 0.6s ease-out;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h2><i class="fas fa-boxes me-3"></i>MFS Payout Batches</h2>
        <p class="page-subtitle mb-0">Manage and monitor batch processing for mobile financial service payouts</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards animate-in">
        <div class="stats-card total-batches">
            <div class="icon-wrapper">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stats-label">Total Batches</div>
            <div class="stats-value">{{ count($batches) }}</div>
        </div>
        
        <div class="stats-card total-amount">
            <div class="icon-wrapper">
                <i class="fas fa-calculator"></i>
            </div>
            <div class="stats-label">Total Amount</div>
            <div class="stats-value">{{ number_format($batches->sum('total_amount'), 0) }} <small style="font-size: 0.8rem; font-weight: 500;">BDT</small></div>
        </div>
        
        <div class="stats-card success-amount">
            <div class="icon-wrapper">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-label">Success Amount</div>
            <div class="stats-value">{{ number_format($batches->sum('success'), 0) }} <small style="font-size: 0.8rem; font-weight: 500;">BDT</small></div>
        </div>
        
        <div class="stats-card failed-amount">
            <div class="icon-wrapper">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stats-label">Failed Amount</div>
            <div class="stats-value">{{ number_format($batches->sum('failed'), 0) }} <small style="font-size: 0.8rem; font-weight: 500;">BDT</small></div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="table-card animate-in" style="animation-delay: 0.1s;">
        <div class="table-header">
            <i class="fas fa-table"></i>
            <h5>Batch Processing Overview</h5>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover" id="mfsBatchesTable">
                <thead>
                    <tr>
                        <th>Batch ID</th>
                        <th>Merchant</th>
                        <th>Total Amount</th>
                        <th>Total Count</th>
                        <th>Pending</th>
                        <th>Success</th>
                        <th>Failed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batches as $batch)
                        <tr>
                            <td>
                                <span class="batch-id">{{ $batch->batch_id }}</span>
                            </td>
                            <td>{{ $batch->merchant_id }}</td>
                            <td>
                                <span class="amount-display">{{ number_format($batch->total_amount, 2) }}</span>
                                <small class="text-muted">BDT</small>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $batch->count }}</span></td>
                            <td>
                                <span class="status-badge status-pending">
                                    <i class="fas fa-clock"></i>
                                    {{ number_format($batch->pending, 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-success">
                                    <i class="fas fa-check"></i>
                                    {{ number_format($batch->success, 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-failed">
                                    <i class="fas fa-times"></i>
                                    {{ number_format($batch->failed, 2) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-view btn-sm" onclick="viewBatchDetails('{{ $batch->batch_id }}')">
                                    <i class="fas fa-eye me-1"></i> View Details
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Batch Details Modal -->
<div class="modal fade" id="batchDetailsModal" tabindex="-1" aria-labelledby="batchDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="batchDetailsModalLabel">
                    <i class="fas fa-file-alt me-2"></i>Batch Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalContent">
                    <!-- Batch info will be loaded here -->
                    <div class="loading-overlay">
                        <div class="loading-spinner"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
                <button type="button" class="btn btn-view" id="exportBatchBtn">
                    <i class="fas fa-download me-1"></i> Export to Excel
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(function () {
    let batchTable = null;
    let currentBatchId = null;

    // Initialize main table
    const dt = $('#mfsBatchesTable').DataTable({
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
        language: {
            search: "Search batches:",
            lengthMenu: "Show _MENU_ batches per page",
            info: "Showing _START_ to _END_ of _TOTAL_ batches",
            infoEmpty: "No batches found",
            infoFiltered: "(filtered from _MAX_ total batches)"
        }
    });

    // Add smooth animations on page load
    document.addEventListener('DOMContentLoaded', function() {
        const statsCards = document.querySelectorAll('.stats-card');
        statsCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 + (index * 100));
        });

        // Add hover effects to stats cards
        statsCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });

    // Function to view batch details
    window.viewBatchDetails = function(batchId) {
        currentBatchId = batchId;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('batchDetailsModal'));
        modal.show();
        
        // Update modal title
        document.getElementById('batchDetailsModalLabel').innerHTML = 
            '<i class="fas fa-file-alt me-2"></i>Batch Details: ' + batchId;
        
        // Show loading state
        document.getElementById('modalContent').innerHTML = `
            <div class="loading-overlay">
                <div class="loading-spinner"></div>
            </div>
        `;
        
        // Make AJAX call to get real batch details using your existing route
        $.get('/mfs-payout/batch/' + batchId)
            .done(function(response) {
                loadBatchDetails(batchId, response);
            })
            .fail(function(xhr) {
                showErrorMessage('Failed to load batch details: ' + (xhr.responseJSON?.message || 'Unknown error'));
            });
    };

    function loadBatchDetails(batchId, data) {
        // Calculate statistics from real data
        const totalCount = data.payouts.length;
        const totalAmount = data.payouts.reduce((sum, p) => sum + parseFloat(p.amount), 0);
        const successCount = data.payouts.filter(p => p.status === 'Success').length;
        const successRate = totalCount > 0 ? Math.round((successCount / totalCount) * 100) : 0;
        
        const content = `
            <div class="batch-info">
                <div class="batch-info-grid">
                    <div class="batch-info-item">
                        <div class="batch-info-label">Batch ID</div>
                        <div class="batch-info-value">${batchId}</div>
                    </div>
                    <div class="batch-info-item">
                        <div class="batch-info-label">Total Amount</div>
                        <div class="batch-info-value">${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} BDT</div>
                    </div>
                    <div class="batch-info-item">
                        <div class="batch-info-label">Total Count</div>
                        <div class="batch-info-value">${totalCount}</div>
                    </div>
                    <div class="batch-info-item">
                        <div class="batch-info-label">Success Rate</div>
                        <div class="batch-info-value">${successRate}%</div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="batchPayoutTable">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Merchant ID</th>
                            <th>Amount</th>
                            <th>Wallet</th>
                            <th>Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.payouts.map(payout => `
                            <tr>
                                <td><code>${payout.reference_key}</code></td>
                                <td>${payout.merchant_id}</td>
                                <td><strong>${parseFloat(payout.amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong> <small class="text-muted">BDT</small></td>
                                <td>${payout.wallet_number}</td>
                                <td><span class="badge bg-light text-dark">${payout.method}</span></td>
                                <td>
                                    <span class="badge ${payout.status === 'Success' ? 'bg-success' : (payout.status === 'Failed' ? 'bg-danger' : 'bg-warning')}">
                                        ${payout.status}
                                    </span>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
        
        document.getElementById('modalContent').innerHTML = content;
        
        // Initialize DataTable for batch details
        if (batchTable) {
            batchTable.destroy();
        }
        
        batchTable = $('#batchPayoutTable').DataTable({
            pageLength: 10,
            responsive: true,
            language: {
                search: "Search transactions:",
                lengthMenu: "Show _MENU_ transactions per page",
                info: "Showing _START_ to _END_ of _TOTAL_ transactions"
            }
        });
    }

    function showErrorMessage(message) {
        document.getElementById('modalContent').innerHTML = `
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Error:</strong> ${message}
            </div>
        `;
    }

    // Export batch data
    document.getElementById('exportBatchBtn').addEventListener('click', function() {
        if (batchTable && currentBatchId) {
            // Trigger export - you can customize this
            batchTable.button('.buttons-excel').trigger();
        }
    });

    // Clean up on modal close
    document.getElementById('batchDetailsModal').addEventListener('hidden.bs.modal', function() {
        if (batchTable) {
            batchTable.destroy();
            batchTable = null;
        }
        currentBatchId = null;
    });
});
</script>
@endsection