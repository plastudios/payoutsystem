@extends('layouts.app')

@section('title', 'Merchant Balance History')

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
            color: #8b5cf6;
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

        .form-control,
        .form-select {
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background: #ffffff;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
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
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(139, 92, 246, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(139, 92, 246, 0.3);
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
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
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-title {
            display: flex;
            align-items: center;
        }

        .table-title i {
            color: #8b5cf6;
            margin-right: 8px;
            font-size: 1.1rem;
        }

        .table-title h5 {
            margin: 0;
            font-weight: 600;
            color: #1e293b;
            font-size: 1.1rem;
        }

        .table-stats {
            display: flex;
            gap: 20px;
            font-size: 0.875rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-weight: 700;
            font-size: 1.1rem;
            color: #1e293b;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* DataTables Styling */
        .dataTables_wrapper {
            font-family: 'Inter', sans-serif;
        }

        .dataTables_length select {
            border: 1.5px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 0.875rem;
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
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
            border-color: #8b5cf6 !important;
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
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
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

        .type-allocated {
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
            font-weight: 600;
        }

        .amount-negative {
            color: #dc2626;
            font-weight: 600;
        }

        .amount-neutral {
            color: #6b7280;
            font-weight: 600;
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
            .page-subtitle {
                font-size: 0.8rem;
            }

            .table-stats {
                flex-wrap: wrap;
            }

            .filter-card .row.g-3 > div {
                margin-bottom: 12px;
            }
        }

        @media (max-width: 768px) {

            .filter-card,
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

            .filter-header h5,
            .table-title h5 {
                font-size: 1rem;
            }

            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .table-stats {
                gap: 12px;
                width: 100%;
                justify-content: space-between;
            }

            .stat-item {
                flex: 1;
            }

            .stat-value {
                font-size: 0.95rem;
            }

            .stat-label {
                font-size: 0.65rem;
            }

            .btn {
                padding: 8px 16px;
                font-size: 0.8rem;
            }

            .d-flex.gap-2 {
                flex-direction: column;
                gap: 8px !important;
            }

            .d-flex.gap-2 .btn {
                width: 100%;
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

            .merchant-id {
                font-size: 0.7rem;
                padding: 3px 6px;
            }

            .type-badge {
                font-size: 0.65rem;
                padding: 4px 10px;
            }

            .amount-positive,
            .amount-negative,
            .amount-neutral {
                font-size: 0.8rem;
            }

            .date-day {
                font-size: 0.75rem;
            }

            .date-time {
                font-size: 0.65rem;
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

            .filter-header i,
            .table-title i {
                font-size: 0.9rem;
            }

            .table-stats {
                flex-direction: column;
                gap: 8px;
            }

            .stat-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px;
                background: #f8fafc;
                border-radius: 8px;
            }

            .stat-value {
                order: 2;
            }

            .stat-label {
                order: 1;
            }

            /* Stack filter inputs vertically */
            .filter-card .col-md-3,
            .filter-card .col-md-6 {
                width: 100%;
                margin-bottom: 8px;
            }

            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .type-badge i,
            .amount-positive i,
            .amount-negative i {
                font-size: 0.6rem;
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

        /* Enhanced Merchant ID styling */
        .merchant-id {
            font-family: 'Monaco', 'Consolas', monospace;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #1e293b;
        }

        /* Enhanced date styling */
        .date-display {
            line-height: 1.2;
        }

        .date-day {
            font-weight: 600;
            color: #1e293b;
        }

        .date-time {
            font-size: 0.75rem;
            color: #6b7280;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h2><i class="fas fa-credit-card me-3"></i>Merchant Balance History</h2>
            <p class="page-subtitle mb-0">Track all balance transactions and account movements across merchants</p>
        </div>

        <!-- Filters Card -->
        <div class="filter-card animate-in">
            <div class="filter-header">
                <i class="fas fa-filter"></i>
                <h5>Transaction Filters</h5>
            </div>

            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $start }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $end }}">
                </div>

                @if(!isset($isMerchant) || !$isMerchant)
                    <div class="col-md-3">
                        <label class="form-label">Merchant</label>
                        <select name="merchant_id" class="form-select">
                            <option value="all" {{ ($merchantId ?? 'all') === 'all' ? 'selected' : '' }}>All Merchants</option>
                            @foreach($merchants as $m)
                                <option value="{{ $m }}" {{ ($merchantId ?? '') === $m ? 'selected' : '' }}>
                                    {{ $m }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-md-{{ isset($isMerchant) && $isMerchant ? '6' : '3' }} d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Apply Filters
                    </button>
                    <a href="{{ route('merchant.balances') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="table-card animate-in" style="animation-delay: 0.1s;">
            <div class="table-header">
                <div class="table-title">
                    <i class="fas fa-table"></i>
                    <h5>Balance Transaction Records</h5>
                </div>
                <div class="table-stats">
                    <div class="stat-item">
                        <div class="stat-value" id="total-records">{{ count($balances) }}</div>
                        <div class="stat-label">Total Records</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value text-success" id="credit-amount">
                            {{ number_format($balances->where('type', 'credit')->sum('amount'), 0) }}
                        </div>
                        <div class="stat-label">Credits (BDT)</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value text-danger" id="debit-amount">
                            {{ number_format($balances->where('type', 'debit')->sum('amount'), 0) }}
                        </div>
                        <div class="stat-label">Debits (BDT)</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="balanceTable">
                    <thead>
                        <tr>
                            <th>Merchant ID</th>
                            <th>Transaction Type</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($balances as $balance)
                            <tr>
                                <td>
                                    <span class="merchant-id">{{ $balance->merchant_id }}</span>
                                </td>
                                <td>
                                    <span class="type-badge type-{{ strtolower($balance->type) }}">
                                        @if(strtolower($balance->type) === 'credit')
                                            <i class="fas fa-arrow-down me-1"></i>
                                        @elseif(strtolower($balance->type) === 'debit')
                                            <i class="fas fa-arrow-up me-1"></i>
                                        @elseif(strtolower($balance->type) === 'allocated')
                                            <i class="fas fa-plus me-1"></i>
                                        @else
                                            <i class="fas fa-edit me-1"></i>
                                        @endif
                                        {{ ucfirst($balance->type) }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="amount-{{ strtolower($balance->type) === 'credit' ? 'positive' : (strtolower($balance->type) === 'debit' ? 'negative' : 'neutral') }}">
                                        @if(strtolower($balance->type) === 'credit')
                                            +{{ number_format($balance->amount, 2) }}
                                        @elseif(strtolower($balance->type) === 'debit')
                                            -{{ number_format($balance->amount, 2) }}
                                        @else
                                            {{ number_format($balance->amount, 2) }}
                                        @endif
                                        <small class="text-muted">BDT</small>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $balance->remarks ?: 'No remarks' }}</span>
                                </td>
                                <td>
                                    <div class="date-display">
                                        <div class="date-day">{{ $balance->created_at->format('M d, Y') }}</div>
                                        <div class="date-time">{{ $balance->created_at->format('H:i:s') }}</div>
                                    </div>
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
            const dt = $('#balanceTable').DataTable({
                dom: 'Brtip',
                searching: false,
                ordering: false,
                pageLength: 50,
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
                responsive: true,
                language: {
                    lengthMenu: "Show _MENU_ transactions per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ transactions",
                    infoEmpty: "No transactions found",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });

            // Add smooth animations on page load
            document.addEventListener('DOMContentLoaded', function () {
                // Animate table rows with stagger effect
                const tableRows = document.querySelectorAll('#balanceTable tbody tr');
                tableRows.forEach((row, index) => {
                    if (index < 10) { // Only animate first 10 rows for performance
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-20px)';

                        setTimeout(() => {
                            row.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                            row.style.opacity = '1';
                            row.style.transform = 'translateX(0)';
                        }, 50 + (index * 50));
                    }
                });

                // Add hover effects to stat items
                const statItems = document.querySelectorAll('.stat-item');
                statItems.forEach(item => {
                    item.addEventListener('mouseenter', function () {
                        this.style.transform = 'scale(1.05)';
                        this.style.transition = 'transform 0.2s ease';
                    });

                    item.addEventListener('mouseleave', function () {
                        this.style.transform = 'scale(1)';
                    });
                });

                // Enhanced filter form animations
                const filterInputs = document.querySelectorAll('.filter-card input, .filter-card select');
                filterInputs.forEach(input => {
                    input.addEventListener('focus', function () {
                        this.parentElement.style.transform = 'scale(1.02)';
                        this.parentElement.style.transition = 'transform 0.2s ease';
                    });

                    input.addEventListener('blur', function () {
                        this.parentElement.style.transform = 'scale(1)';
                    });
                });
            });

            // Update statistics when table is filtered or paginated
            dt.on('draw', function () {
                const visibleData = dt.rows({ page: 'current' }).data();
                let totalRecords = visibleData.length;

                // You can add more dynamic statistics here if needed
                console.log('Visible records:', totalRecords);
            });

            // Add tooltip functionality for transaction types
            document.querySelectorAll('.type-badge').forEach(badge => {
                const type = badge.textContent.trim().toLowerCase();
                let tooltip = '';

                switch (type) {
                    case 'credit':
                        tooltip = 'Money added to merchant account';
                        break;
                    case 'debit':
                        tooltip = 'Money deducted from merchant account';
                        break;
                    case 'allocated':
                        tooltip = 'Initial balance allocation';
                        break;
                    default:
                        tooltip = 'Balance adjustment or correction';
                }

                badge.setAttribute('title', tooltip);
            });
        });
    </script>
@endsection