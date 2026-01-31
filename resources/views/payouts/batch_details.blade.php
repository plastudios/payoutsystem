@extends('layouts.app')
@section('title', 'Batch: ' . $batchId)

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
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .page-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.75rem;
    }

    .batch-id-display {
        font-family: 'Monaco', 'Consolas', monospace;
        background: rgba(255, 255, 255, 0.15);
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.9rem;
        display: inline-block;
        margin-top: 8px;
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, 0.25);
        color: white;
        transform: translateX(-2px);
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        background: var(--card-color, #3b82f6);
        border-radius: 16px 16px 0 0;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .stats-card .icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        background: var(--card-color, #3b82f6);
        color: white;
        font-size: 1.25rem;
    }

    .stats-card .stats-label {
        color: #64748b;
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 8px;
    }

    .stats-card .stats-value {
        color: #1e293b;
        font-weight: 700;
        font-size: 1.75rem;
        margin: 0;
        line-height: 1.1;
    }

    .stats-card.total-count { --card-color: #3b82f6; }
    .stats-card.total-amount { --card-color: #8b5cf6; }
    .stats-card.success-count { --card-color: #10b981; }
    .stats-card.failed-count { --card-color: #ef4444; }
    .stats-card.pending-count { --card-color: #f59e0b; }

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

    @media (max-width: 480px) {
        .stats-cards {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    @php
        $totalCount = count($payouts);
        $totalAmount = $payouts->sum('amount');
        $successCount = $payouts->where('status', 'Success')->count();
        $failedCount = $payouts->where('status', 'Failed')->count();
        $pendingCount = $payouts->where('status', 'Pending')->count();
        $successAmount = $payouts->where('status', 'Success')->sum('amount');
        $successRate = $totalCount > 0 ? round(($successCount / $totalCount) * 100, 1) : 0;
    @endphp

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h2><i class="fas fa-file-invoice me-3"></i>Batch Details</h2>
            <div class="batch-id-display">
                <i class="fas fa-hashtag me-2"></i>{{ $batchId }}
            </div>
        </div>
        <button onclick="window.history.back()" class="btn btn-back">
            <i class="fas fa-arrow-left me-2"></i>Back
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stats-card total-count">
            <div class="icon-wrapper">
                <i class="fas fa-list"></i>
            </div>
            <div class="stats-label">Total Transactions</div>
            <div class="stats-value">{{ $totalCount }}</div>
        </div>

        <div class="stats-card total-amount">
            <div class="icon-wrapper">
                <i class="fas fa-calculator"></i>
            </div>
            <div class="stats-label">Total Amount</div>
            <div class="stats-value">{{ number_format($totalAmount, 0) }} <small style="font-size: 0.8rem; font-weight: 500;">BDT</small></div>
        </div>

        <div class="stats-card success-count">
            <div class="icon-wrapper">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-label">Success ({{ $successRate }}%)</div>
            <div class="stats-value">{{ $successCount }}</div>
        </div>

        <div class="stats-card failed-count">
            <div class="icon-wrapper">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stats-label">Failed</div>
            <div class="stats-value">{{ $failedCount }}</div>
        </div>

        @if($pendingCount > 0)
        <div class="stats-card pending-count">
            <div class="icon-wrapper">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-label">Pending</div>
            <div class="stats-value">{{ $pendingCount }}</div>
        </div>
        @endif
    </div>

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-header">
            <i class="fas fa-table"></i>
            <h5>Transaction Details</h5>
        </div>

        <div class="table-responsive">
            <table id="detailTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Merchant ID</th>
                        <th>Amount</th>
                        <th>Beneficiary</th>
                        <th>Bank</th>
                        <th>Account</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payouts as $payout)
                    <tr>
                        <td><code>{{ $payout->referenceKey }}</code></td>
                        <td>{{ $payout->merchant_id }}</td>
                        <td><strong>{{ number_format($payout->amount, 2) }}</strong> <small class="text-muted">BDT</small></td>
                        <td>{{ $payout->beneficiaryName }}</td>
                        <td>{{ $payout->bankShortCode }}</td>
                        <td>{{ $payout->beneficiaryAcc }}</td>
                        <td>
                            <span class="badge 
                                {{ $payout->status === 'Success' ? 'bg-success' : 
                                   ($payout->status === 'Failed' ? 'bg-danger' : 'bg-warning') }}">
                                {{ $payout->status }}
                            </span>
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
        $('#detailTable').DataTable({
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
            order: [[0, 'asc']],
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
    });
</script>
@endsection
