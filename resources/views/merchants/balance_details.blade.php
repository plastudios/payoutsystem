@extends('layouts.app')

@section('title', 'Merchant Balance Details')

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

    .page-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        transform: translate(-20px, 20px);
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
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .stats-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
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
        background: var(--card-color, #6366f1);
        border-radius: 16px 16px 0 0;
    }

    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
    }

    .stats-card .icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        background: var(--card-color, #6366f1);
        color: white;
        font-size: 1.25rem;
    }

    .stats-card .stats-label {
        color: #64748b;
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 8px;
        line-height: 1.2;
    }

    .stats-card .stats-value {
        color: #1e293b;
        font-weight: 700;
        font-size: 1.75rem;
        margin: 0;
        line-height: 1.1;
        font-family: 'Inter', sans-serif;
    }

    .stats-card .stats-note {
        color: #9ca3af;
        font-size: 0.75rem;
        margin-top: 8px;
        font-style: italic;
    }

    /* Card Color Variations */
    .stats-card.total-records {
        --card-color: #6366f1;
    }

    .stats-card.total-credit {
        --card-color: #10b981;
    }

    .stats-card.total-debit {
        --card-color: #ef4444;
    }

    .stats-card.current-balance {
        --card-color: #8b5cf6;
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
        color: #6366f1;
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
        border-color: #6366f1;
        outline: none;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
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
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
        border-color: #6366f1 !important;
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

    /* Transaction Type Badges */
    .type-badge {
        padding: 6px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .type-credit {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
        border: 1px solid #86efac;
    }

    .type-debit {
        background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
        color: #dc2626;
        border: 1px solid #fca5a5;
    }

    .type-allocation {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1d4ed8;
        border: 1px solid #93c5fd;
    }

    .type-adjustment {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border: 1px solid #f59e0b;
    }

    /* Amount Styling */
    .amount-positive {
        color: #059669;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .amount-negative {
        color: #dc2626;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .amount-neutral {
        color: #6b7280;
        font-weight: 700;
    }

    /* Serial Number Styling */
    .serial-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
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

    /* Date Display */
    .date-display {
        line-height: 1.2;
    }

    .date-day {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.875rem;
    }

    .date-time {
        font-size: 0.75rem;
        color: #6b7280;
    }

    /* Responsive */
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
        
        .stats-cards {
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        .stats-card {
            padding: 20px;
        }
        
        .stats-card .stats-value {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .stats-cards {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }

    /* Loading Animation */
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
        <h2><i class="fas fa-wallet me-3"></i>Merchant Balance Details</h2>
        <p class="page-subtitle mb-0">Comprehensive view of all balance transactions and account movements</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards animate-in">
        <div class="stats-card total-records">
            <div class="icon-wrapper">
                <i class="fas fa-list-ol"></i>
            </div>
            <div class="stats-label">Total Records</div>
            <div class="stats-value">{{ count($balances) }}</div>
            <div class="stats-note">All time transactions</div>
        </div>
        
        <div class="stats-card total-credit">
            <div class="icon-wrapper">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="stats-label">Total Credits</div>
            <div class="stats-value">{{ number_format($balances->where('type', 'credit')->sum('amount'), 0) }} <small style="font-size: 0.8rem; font-weight: 500;">BDT</small></div>
            <div class="stats-note">Money received</div>
        </div>
        
        <div class="stats-card total-debit">
            <div class="icon-wrapper">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stats-label">Total Debits</div>
            <div class="stats-value">{{ number_format($balances->where('type', 'debit')->sum('amount'), 0) }} <small style="font-size: 0.8rem; font-weight: 500;">BDT</small></div>
            <div class="stats-note">Money spent</div>
        </div>
        
        <div class="stats-card current-balance">
            <div class="icon-wrapper">
                <i class="fas fa-balance-scale"></i>
            </div>
            <div class="stats-label">Net Balance</div>
            <div class="stats-value">{{ number_format($balances->where('type', 'credit')->sum('amount') - $balances->where('type', 'debit')->sum('amount'), 0) }} <small style="font-size: 0.8rem; font-weight: 500;">BDT</small></div>
            <div class="stats-note">Credits - Debits</div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="table-card animate-in" style="animation-delay: 0.1s;">
        <div class="table-header">
            <i class="fas fa-table"></i>
            <h5>Transaction History</h5>
        </div>
        
        <table class="table table-hover" id="balanceTable">
            <thead>
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Transaction Type</th>
                    <th>Amount</th>
                    <th>Remarks</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($balances as $index => $row)
                <tr>
                    <td>
                        <div class="serial-number">{{ $index + 1 }}</div>
                    </td>
                    <td>
                        <span class="type-badge type-{{ strtolower($row->type) }}">
                            @if(strtolower($row->type) === 'credit')
                                <i class="fas fa-plus"></i>
                            @elseif(strtolower($row->type) === 'debit')
                                <i class="fas fa-minus"></i>
                            @else
                                <i class="fas fa-edit"></i>
                            @endif
                            {{ ucfirst($row->type) }}
                        </span>
                    </td>
                    <td>
                        <span class="amount-{{ $row->type === 'debit' ? 'negative' : 'positive' }}">
                            @if($row->type === 'credit')
                                <i class="fas fa-plus-circle"></i>
                                +{{ number_format($row->amount, 2) }}
                            @elseif($row->type === 'debit')
                                <i class="fas fa-minus-circle"></i>
                                -{{ number_format($row->amount, 2) }}
                            @else
                                {{ number_format($row->amount, 2) }}
                            @endif
                            <small class="text-muted">BDT</small>
                        </span>
                    </td>
                    <td>
                        <span class="text-muted">{{ $row->remarks ?: 'No remarks provided' }}</span>
                    </td>
                    <td>
                        <div class="date-display">
                            <div class="date-day">{{ \Carbon\Carbon::parse($row->created_at)->format('M d, Y') }}</div>
                            <div class="date-time">{{ \Carbon\Carbon::parse($row->created_at)->format('H:i:s') }}</div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
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
$(function() {
    // Initialize DataTable
    const dt = $('#balanceTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy me-1"></i> Copy',
                className: 'dt-button'
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv me-1"></i> CSV',
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
        order: [[4, 'desc']], // Sort by date column (index 4) in descending order
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
        // Animate stats cards with stagger effect
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
                this.style.transform = 'translateY(-6px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Animate table rows on first load
        const tableRows = document.querySelectorAll('#balanceTable tbody tr');
        if (tableRows.length <= 20) { // Only animate if not too many rows
            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    row.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                    row.style.opacity = '1';
                    row.style.transform = 'translateX(0)';
                }, 200 + (index * 30));
            });
        }
    });

    // Add visual feedback for amount interactions
    document.querySelectorAll('.amount-positive, .amount-negative').forEach(amount => {
        amount.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        amount.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Add tooltip for transaction types
    document.querySelectorAll('.type-badge').forEach(badge => {
        const type = badge.textContent.trim().toLowerCase();
        let tooltip = '';
        
        switch(type) {
            case 'credit':
                tooltip = 'Money added to account';
                break;
            case 'debit':
                tooltip = 'Money deducted from account';
                break;
            default:
                tooltip = 'Account adjustment or correction';
        }
        
        badge.setAttribute('title', tooltip);
    });
});
</script>
@endsection