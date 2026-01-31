@extends('layouts.app')

@section('title', 'Payout Summary Report')

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

    .form-label {
        font-weight: 600;
        color: #374151;
        font-size: 0.875rem;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        background: #ffffff;
    }

    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .btn {
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        border: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    }

    .btn-outline-secondary {
        border: 1.5px solid #e2e8f0;
        color: #6b7280;
        background: white;
    }

    .btn-outline-secondary:hover {
        background: #f8fafc;
        border-color: #d1d5db;
        color: #374151;
    }

    /* KPI Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .kpi-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--kpi-color, #3b82f6);
        border-radius: 16px 16px 0 0;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .kpi-card .icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        background: var(--kpi-color, #3b82f6);
        color: white;
        font-size: 1.25rem;
    }

    .kpi-card .kpi-label {
        color: #64748b;
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 8px;
        line-height: 1.2;
    }

    .kpi-card .kpi-value {
        color: #1e293b;
        font-weight: 700;
        font-size: 1.75rem;
        margin: 0;
        line-height: 1.1;
        font-family: 'Inter', sans-serif;
    }

    .kpi-card .kpi-note {
        color: #9ca3af;
        font-size: 0.75rem;
        margin-top: 8px;
        font-style: italic;
    }

    /* KPI Color Variations */
    .kpi-card.credit {
        --kpi-color: #10b981;
    }

    .kpi-card.debit {
        --kpi-color: #f59e0b;
    }

    .kpi-card.balance {
        --kpi-color: #8b5cf6;
    }

    .kpi-card.success {
        --kpi-color: #10b981;
    }

    .kpi-card.failed {
        --kpi-color: #ef4444;
    }

    .kpi-card.pending {
        --kpi-color: #6b7280;
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

    /* API Response Styling */
    pre {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 8px;
        font-size: 0.75rem;
        max-height: 100px;
        overflow-y: auto;
        margin: 0;
        color: #374151;
        font-family: 'Monaco', 'Consolas', monospace;
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

    /* Responsive */
    @media (max-width: 992px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .filter-card, .table-card {
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
            font-size: 0.8rem;
        }
        
        .kpi-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        
        .kpi-card {
            padding: 20px;
        }
        
        .kpi-card .kpi-value {
            font-size: 1.5rem;
        }

        .btn {
            padding: 8px 16px;
            font-size: 0.8rem;
        }

        .filter-header h5 {
            font-size: 1rem;
        }

        /* Make buttons stack on mobile */
        .d-flex.gap-2 {
            flex-direction: column;
            gap: 8px !important;
        }

        .d-flex.gap-2 .btn {
            width: 100%;
        }

        /* Improve table readability on mobile */
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

        pre {
            font-size: 0.65rem;
            max-height: 80px;
        }

        .badge {
            font-size: 0.7rem;
            padding: 4px 8px;
        }

        /* Improve DataTables mobile controls */
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
    }

    @media (max-width: 576px) {
        .kpi-card .icon-wrapper {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .kpi-card .kpi-value {
            font-size: 1.25rem;
        }

        .kpi-card .kpi-label {
            font-size: 0.8rem;
        }

        /* Stack filter form vertically on very small screens */
        .filter-card .row.g-3 > div {
            margin-bottom: 12px;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
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
        <h2><i class="fas fa-chart-bar me-3"></i>Payout Summary Report</h2>
        <p class="page-subtitle mb-0">Comprehensive overview of payout transactions and financial metrics</p>
    </div>

    <!-- Filters Card -->
    <div class="filter-card animate-in">
        <div class="filter-header">
            <i class="fas fa-filter"></i>
            <h5>Report Filters</h5>
        </div>
        
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Merchant</label>
                @if(auth()->user()->role === 'merchant')
                    <input class="form-control" value="{{ auth()->user()->merchant_id }}" disabled>
                @else
                    <select name="merchant_id" class="form-select">
                        <option value="all" {{ ($filters['merchant_id'] ?? 'all') === 'all' ? 'selected' : '' }}>All Merchants</option>
                        @foreach($merchants as $m)
                            <option value="{{ $m }}" {{ ($filters['merchant_id'] ?? '') === $m ? 'selected' : '' }}>
                                {{ $m }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
            
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] ?? '' }}">
            </div>
            
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] ?? '' }}">
            </div>
            
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i> Apply Filters
                </button>
                <a href="{{ route('mfs.payout.summary') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo me-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid animate-in" style="animation-delay: 0.1s;">
        <div class="kpi-card credit">
            <div class="icon-wrapper">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="kpi-label">Total Credit</div>
            <div class="kpi-value">{{ number_format($totals['credit'], 2) }} <small style="font-size: 0.875rem; font-weight: 500;">BDT</small></div>
            <div class="kpi-note">Amount credited in selected period</div>
        </div>

        <div class="kpi-card debit">
            <div class="icon-wrapper">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="kpi-label">Total Debit</div>
            <div class="kpi-value">{{ number_format($totals['debit'], 2) }} <small style="font-size: 0.875rem; font-weight: 500;">BDT</small></div>
            <div class="kpi-note">Amount debited in selected period</div>
        </div>

        <div class="kpi-card balance">
            <div class="icon-wrapper">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="kpi-label">Available Balance</div>
            <div class="kpi-value">{{ number_format($totals['available'], 2) }} <small style="font-size: 0.875rem; font-weight: 500;">BDT</small></div>
            <div class="kpi-note">Current available balance (all-time)</div>
        </div>

        <div class="kpi-card success">
            <div class="icon-wrapper">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="kpi-label">Success Payouts</div>
            <div class="kpi-value">{{ number_format($totals['success'], 2) }} <small style="font-size: 0.875rem; font-weight: 500;">BDT</small></div>
            <div class="kpi-note">Successfully processed in period</div>
        </div>

        <div class="kpi-card failed">
            <div class="icon-wrapper">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="kpi-label">Failed Payouts</div>
            <div class="kpi-value">{{ number_format($totals['failed'], 2) }} <small style="font-size: 0.875rem; font-weight: 500;">BDT</small></div>
            <div class="kpi-note">Failed transactions in period</div>
        </div>

        <div class="kpi-card pending">
            <div class="icon-wrapper">
                <i class="fas fa-clock"></i>
            </div>
            <div class="kpi-label">Pending Payouts</div>
            <div class="kpi-value">{{ number_format($totals['pending'], 2) }} <small style="font-size: 0.875rem; font-weight: 500;">BDT</small></div>
            <div class="kpi-note">Pending transactions in period</div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="table-card animate-in" style="animation-delay: 0.2s;">
        <div class="table-header">
            <i class="fas fa-table"></i>
            <h5>Transaction Details</h5>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover" id="payoutTable">
                <thead>
                    <tr>
                        <th>Batch ID</th>
                        <th>Merchant</th>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Wallet</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>API Response</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payouts as $p)
                        <tr>
                            <td><strong>{{ $p->batch_id }}</strong></td>
                            <td>{{ $p->merchant_id }}</td>
                            <td><code>{{ $p->reference_key }}</code></td>
                            <td><strong>{{ number_format($p->amount, 2) }}</strong> <small class="text-muted">BDT</small></td>
                            <td>{{ $p->wallet_number }}</td>
                            <td><span class="badge bg-light text-dark">{{ $p->method }}</span></td>
                            <td>
                                <span class="badge bg-{{ $p->status === 'Success' ? 'success' : ($p->status === 'Failed' ? 'danger' : 'secondary') }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            @php $resp = json_decode($p->api_response, true); @endphp
                            <td><pre>{{ json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre></td>
                            <td>
                                <div>{{ \Carbon\Carbon::parse($p->created_at)->format('M d, Y') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }}</small>
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
    const dt = $('#payoutTable').DataTable({
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
        order: [[8, 'desc']], // Date column
        responsive: true,
        language: {
            search: "Search transactions:",
            lengthMenu: "Show _MENU_ transactions per page",
            info: "Showing _START_ to _END_ of _TOTAL_ transactions",
            infoEmpty: "No transactions found",
            infoFiltered: "(filtered from _MAX_ total transactions)"
        }
    });

    // Add smooth animations on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Animate KPI cards with stagger effect
        const kpiCards = document.querySelectorAll('.kpi-card');
        kpiCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 + (index * 100));
        });

        // Add hover effects to KPI cards
        kpiCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });

    // Enhanced filter form animations
    const filterInputs = document.querySelectorAll('.filter-card input, .filter-card select');
    filterInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
            this.parentElement.style.transition = 'transform 0.2s ease';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });
});
</script>
@endsection