@extends('layouts.app')

@section('title', 'All MFS Payouts')

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

    .bulk-actions-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .bulk-actions-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .bulk-actions-title {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #1e293b;
        font-weight: 600;
        font-size: 1rem;
    }

    .bulk-count {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
    }

    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);
    }

    .btn-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
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

    /* Button Group Styling */
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

    /* Checkbox Styling */
    input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #3b82f6;
        cursor: pointer;
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
    @media (max-width: 768px) {
        .filter-card, .bulk-actions-card, .table-card {
            padding: 16px;
            margin-bottom: 16px;
        }
        
        .page-header {
            padding: 16px;
        }
        
        .page-header h2 {
            font-size: 1.5rem;
        }
        
        .btn {
            padding: 8px 16px;
            font-size: 0.8rem;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .bulk-actions-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
    }
</style>
@endpush

@section('content')
@php
    $canUpdate = in_array(auth()->user()->role ?? '', ['admin','author','checker','maker']);
    $f = $filter ?? ['start_date' => '', 'end_date' => '', 'status' => 'all', 'merchant_id' => 'all'];
@endphp

<!-- Page Header -->
<div class="page-header">
    <h2><i class="fas fa-mobile-alt me-3"></i>All MFS Payouts</h2>
    <p class="mb-0 opacity-90">Manage and monitor mobile financial service transactions</p>
</div>

<!-- Filters Card -->
<div class="filter-card">
    <div class="filter-header">
        <i class="fas fa-filter"></i>
        <h5>Filter Payouts</h5>
    </div>
    
    <form method="GET" class="row g-3">
        <div class="col-md-3">
            <label class="form-label">From Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ $f['start_date'] }}">
        </div>
        
        <div class="col-md-3">
            <label class="form-label">To Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ $f['end_date'] }}">
        </div>

        <div class="col-md-2">
            <label class="form-label">Status</label>
            @php $sel = $f['status'] ?? 'all'; @endphp
            <select name="status" class="form-select">
                <option value="all" {{ $sel==='all' ? 'selected' : '' }}>All Status</option>
                <option value="Pending" {{ $sel==='Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Success" {{ $sel==='Success' ? 'selected' : '' }}>Success</option>
                <option value="Failed" {{ $sel==='Failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Merchant</label>
            @if(auth()->user()->role === 'merchant')
                <input class="form-control" value="{{ auth()->user()->merchant_id }}" disabled>
                <input type="hidden" name="merchant_id" value="{{ auth()->user()->merchant_id }}">
            @else
                @php $mid = $f['merchant_id'] ?? 'all'; @endphp
                <select name="merchant_id" class="form-select">
                    <option value="all" {{ $mid==='all' ? 'selected' : '' }}>All Merchants</option>
                    @isset($merchants)
                        @foreach($merchants as $m)
                            <option value="{{ $m }}" {{ $mid===$m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    @endisset
                </select>
            @endif
        </div>

        <div class="col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search me-1"></i> Apply
            </button>
            <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                <i class="fas fa-redo me-1"></i> Reset
            </a>
        </div>
    </form>
</div>

<!-- Bulk Actions Card -->
@if($canUpdate)
<div class="bulk-actions-card">
    <div class="bulk-actions-header">
        <div class="bulk-actions-title">
            <i class="fas fa-tasks"></i>
            <span>Bulk Actions</span>
        </div>
        <span id="bulk-count" class="bulk-count" style="display: none;">0 selected</span>
    </div>
    
    <div class="d-flex gap-2 flex-wrap">
        <button id="bulk-success" class="btn btn-success btn-sm" disabled>
            <i class="fas fa-check me-1"></i> Mark as Success
        </button>
        <button id="bulk-failed" class="btn btn-danger btn-sm" disabled>
            <i class="fas fa-times me-1"></i> Mark as Failed
        </button>
        <button id="bulk-delete" class="btn btn-outline-danger btn-sm" disabled>
            <i class="fas fa-trash me-1"></i> Delete Selected
        </button>
    </div>
</div>
@endif

<!-- Table Card -->
<div class="table-card">
    <div class="table-header">
        <i class="fas fa-table"></i>
        <h5>Payout Records</h5>
    </div>
    
    <table class="table table-hover" id="payoutTable">
        <thead>
            <tr>
                @if($canUpdate)
                    <th style="width:40px;"><input type="checkbox" id="select-all"></th>
                @endif
                <th>Batch ID</th>
                <th>Merchant</th>
                <th>Reference</th>
                <th>Amount</th>
                <th>Wallet</th>
                <th>Method</th>
                <th>Status</th>
                <th>API Response</th>
                <th>Date</th>
                @if($canUpdate)
                    <th style="width:200px;">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($payouts as $payout)
            <tr data-id="{{ $payout->id }}">
                @if($canUpdate)
                    <td><input type="checkbox" class="row-check"></td>
                @endif
                <td><strong>{{ $payout->batch_id }}</strong></td>
                <td>{{ $payout->merchant_id }}</td>
                <td><code>{{ $payout->reference_key }}</code></td>
                <td><strong>{{ number_format($payout->amount, 2) }}</strong> <small class="text-muted">BDT</small></td>
                <td>{{ $payout->wallet_number }}</td>
                <td><span class="badge bg-light text-dark">{{ $payout->method }}</span></td>
                <td>
                    <span class="badge bg-{{ $payout->status == 'Success' ? 'success' : ($payout->status == 'Failed' ? 'danger' : 'secondary') }}">
                        {{ $payout->status }}
                    </span>
                </td>
                @php $response = json_decode($payout->api_response, true); @endphp
                <td><pre>{{ json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre></td>
                <td>
                    <div>{{ \Carbon\Carbon::parse($payout->created_at)->format('M d, Y') }}</div>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($payout->created_at)->format('H:i') }}</small>
                </td>

                @if($canUpdate)
                    <td>
                        <div class="action-buttons">
                            <form action="{{ route('mfs.update.status', $payout->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="Success">
                                <button class="btn btn-sm btn-outline-success" title="Mark as Success">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('mfs.update.status', $payout->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="Failed">
                                <button class="btn btn-sm btn-outline-danger" title="Mark as Failed">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            <form action="{{ route('mfs.delete', $payout->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this payout? If it was Success, a credit reversal will be added.');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-secondary" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
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
        order: [],
        responsive: true,
        columnDefs: [
            @if($canUpdate)
            { targets: 0, orderable: false },
            { targets: -1, orderable: false },
            @endif
        ],
        language: {
            search: "Search payouts:",
            lengthMenu: "Show _MENU_ payouts per page",
            info: "Showing _START_ to _END_ of _TOTAL_ payouts",
            infoEmpty: "No payouts found",
            infoFiltered: "(filtered from _MAX_ total payouts)"
        }
    });

    @if($canUpdate)
    const $selectAll   = $('#select-all');
    const $bulkSuccess = $('#bulk-success');
    const $bulkFailed  = $('#bulk-failed');
    const $bulkDelete  = $('#bulk-delete');
    const $bulkCount   = $('#bulk-count');

    function selectedIds() {
        const ids = [];
        $('#payoutTable tbody tr:visible').each(function () {
            const $row = $(this);
            const $chk = $row.find('input.row-check');
            if ($chk.prop('checked')) ids.push($row.data('id'));
        });
        return ids;
    }

    function updateBulkUI() {
        const n = selectedIds().length;
        if (n > 0) {
            $bulkCount.text(n + ' selected').show();
            $bulkSuccess.prop('disabled', false);
            $bulkFailed.prop('disabled', false);
            $bulkDelete.prop('disabled', false);
        } else {
            $bulkCount.hide();
            $bulkSuccess.prop('disabled', true);
            $bulkFailed.prop('disabled', true);
            $bulkDelete.prop('disabled', true);
        }
    }

    dt.on('draw', function () {
        $selectAll.prop('checked', false);
        updateBulkUI();
    });

    $(document).on('change', 'input.row-check', updateBulkUI);

    $selectAll.on('change', function () {
        const checked = $(this).prop('checked');
        $('#payoutTable tbody tr:visible input.row-check').prop('checked', checked);
        updateBulkUI();
    });

    function postBulk(url, payload) {
        return $.ajax({
            method: 'POST',
            url: url,
            data: Object.assign({_token: '{{ csrf_token() }}'}, payload)
        });
    }

    $bulkSuccess.on('click', function () {
        const ids = selectedIds();
        if (!ids.length) return;
        if (!confirm('Set '+ids.length+' payout(s) to Success?')) return;

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Processing...');

        postBulk('{{ route("mfs.bulk.update.status") }}', { ids, status: 'Success' })
            .done(res => { 
                alert(res.message || 'Updated successfully.'); 
                location.reload(); 
            })
            .fail(xhr => {
                alert(xhr.responseJSON?.message || 'Bulk update failed.');
                $(this).prop('disabled', false).html('<i class="fas fa-check me-1"></i> Mark as Success');
            });
    });

    $bulkFailed.on('click', function () {
        const ids = selectedIds();
        if (!ids.length) return;
        if (!confirm('Set '+ids.length+' payout(s) to Failed?')) return;

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Processing...');

        postBulk('{{ route("mfs.bulk.update.status") }}', { ids, status: 'Failed' })
            .done(res => { 
                alert(res.message || 'Updated successfully.'); 
                location.reload(); 
            })
            .fail(xhr => {
                alert(xhr.responseJSON?.message || 'Bulk update failed.');
                $(this).prop('disabled', false).html('<i class="fas fa-times me-1"></i> Mark as Failed');
            });
    });

    $bulkDelete.on('click', function () {
        const ids = selectedIds();
        if (!ids.length) return;
        if (!confirm('Delete '+ids.length+' payout(s)? If any were Success, a reversal credit will be added.')) return;

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Deleting...');

        postBulk('{{ route("mfs.bulk.delete") }}', { ids })
            .done(res => { 
                alert(res.message || 'Deleted successfully.'); 
                location.reload(); 
            })
            .fail(xhr => {
                alert(xhr.responseJSON?.message || 'Bulk delete failed.');
                $(this).prop('disabled', false).html('<i class="fas fa-trash me-1"></i> Delete Selected');
            });
    });
    @endif

    // Add smooth animations
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.filter-card, .bulk-actions-card, .table-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
});
</script>
@endsection