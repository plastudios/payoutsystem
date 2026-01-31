@extends('layouts.app')

@section('title', 'Payout Details')

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

    .badge {
        font-weight: 600;
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 20px;
    }

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
        <h2><i class="fas fa-list-alt me-3"></i>Payout Details</h2>
        <p class="mb-0 opacity-90">Complete list of all payout transactions with API responses</p>
    </div>

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover" id="payoutTable">
                <thead>
                    <tr>
                        <th>Batch ID</th>
                        <th>Merchant ID</th>
                        <th>Ref. Key</th>
                        <th>Amount</th>
                        <th>Beneficiary</th>
                        <th>Bank</th>
                        <th>Account</th>
                        <th>Status</th>
                        <th>API Response</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payouts as $payout)
                    <tr>
                        <td><strong>{{ $payout->batch_id }}</strong></td>
                        <td>{{ $payout->merchant_id }}</td>
                        <td><code>{{ $payout->referenceKey }}</code></td>
                        <td><strong>{{ $payout->amount }}</strong> <small class="text-muted">{{ $payout->currency }}</small></td>
                        <td>{{ $payout->beneficiaryName }}</td>
                        <td>{{ $payout->bankShortCode }}</td>
                        <td>{{ $payout->beneficiaryAcc }}</td>
                        <td>
                            <span class="badge bg-{{ $payout->status == 'Success' ? 'success' : ($payout->status == 'Failed' ? 'danger' : 'secondary') }}">
                                {{ $payout->status }}
                            </span>
                        </td>
                        @php
                            $response = json_decode($payout->api_response, true);
                        @endphp
                        <td><pre>{{ json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre></td>
                        <td>
                            <div>{{ $payout->created_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $payout->created_at->format('H:i') }}</small>
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
        $('#payoutTable').DataTable({
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
            order: [[9, 'desc']],
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
