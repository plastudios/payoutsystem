@extends('layouts.app')

@section('title', 'Webhook Logs')

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

    .table-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    .table-header i {
        color: #8b5cf6;
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

    .btn-view {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
        border: none;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        transition: all 0.2s ease;
    }

    .btn-view:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(139, 92, 246, 0.3);
        color: white;
    }

    .url-display {
        font-family: 'Monaco', 'Consolas', monospace;
        font-size: 0.75rem;
        color: #6b7280;
        word-break: break-all;
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

    /* Modal Styling */
    .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
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

    .modal-body textarea {
        font-family: 'Monaco', 'Consolas', monospace;
        font-size: 0.85rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
    }

    .modal-footer {
        border-top: 1px solid #f1f5f9;
        padding: 16px 24px;
    }

    .btn-copy {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        font-weight: 600;
    }

    .btn-copy:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
        transform: translateY(-1px);
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

        .badge {
            font-size: 0.7rem;
            padding: 4px 8px;
        }

        .btn-view {
            padding: 4px 8px;
            font-size: 0.7rem;
        }

        .url-display {
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

        /* Modal improvements */
        .modal-dialog {
            margin: 10px;
        }

        .modal-header {
            padding: 16px;
        }

        .modal-title {
            font-size: 1.1rem;
        }

        .modal-body {
            padding: 16px;
        }

        .modal-body textarea {
            font-size: 0.75rem;
        }

        .modal-footer {
            padding: 12px 16px;
        }

        .modal-footer .btn {
            font-size: 0.75rem;
            padding: 6px 12px;
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

        .modal-dialog {
            margin: 5px;
        }

        .modal-body textarea {
            font-size: 0.7rem;
            rows: 15;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h2><i class="fas fa-webhook me-3"></i>Webhook Logs</h2>
        <p class="mb-0 opacity-90">Monitor and track all webhook requests and responses</p>
    </div>

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-header">
            <i class="fas fa-table"></i>
            <h5>Webhook Transaction Logs</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="logTable">
                <thead>
                    <tr>
                        <th>Batch ID</th>
                        <th>Merchant ID</th>
                        <th>URL</th>
                        <th>Status Code</th>
                        <th>Request</th>
                        <th>Response</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td><strong>{{ $log->batch_id }}</strong></td>
                        <td><code>{{ $log->merchant_id }}</code></td>
                        <td><div class="url-display">{{ $log->url }}</div></td>
                        <td>
                            <span class="badge bg-{{ $log->status_code == 200 ? 'success' : 'danger' }}">
                                {{ $log->status_code ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-view btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#logModal"
                                    data-type="request"
                                    data-content="{{ htmlentities($log->request_payload) }}">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-view btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#logModal"
                                    data-type="response"
                                    data-content="{{ htmlentities($log->response_payload) }}">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                        </td>
                        <td>
                            <div>{{ $log->created_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="logModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logModalLabel">
            <i class="fas fa-file-code me-2"></i>Webhook Log
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <textarea class="form-control" id="logContent" rows="20" readonly></textarea>
      </div>
      <div class="modal-footer">
        <button class="btn btn-sm btn-copy" onclick="copyLog()">
            <i class="fas fa-copy me-1"></i> Copy to Clipboard
        </button>
        <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Close
        </button>
      </div>
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
    function copyLog() {
        const textarea = document.getElementById("logContent");
        textarea.select();
        textarea.setSelectionRange(0, 99999); // for mobile
        navigator.clipboard.writeText(textarea.value).then(() => {
            // Show success feedback
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Copied!';
            setTimeout(() => {
                btn.innerHTML = originalText;
            }, 2000);
        }).catch(() => {
            // Fallback for older browsers
            document.execCommand("copy");
        });
    }

    const logModal = document.getElementById('logModal');
    logModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const content = button.getAttribute('data-content');
        const type = button.getAttribute('data-type');
        const modalTitle = logModal.querySelector('.modal-title');
        const textarea = logModal.querySelector('#logContent');

        modalTitle.innerHTML = `<i class="fas fa-file-code me-2"></i>Webhook ${type.charAt(0).toUpperCase() + type.slice(1)}`;
        textarea.value = decodeHTMLEntities(content);
    });

    function decodeHTMLEntities(text) {
        const textarea = document.createElement('textarea');
        textarea.innerHTML = text;
        return textarea.value;
    }

    // Initialize DataTable
    $(document).ready(function () {
        $('#logTable').DataTable({
            pageLength: 25,
            order: [[6, 'desc']],
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
            responsive: true,
            language: {
                search: "Search webhook logs:",
                lengthMenu: "Show _MENU_ logs per page",
                info: "Showing _START_ to _END_ of _TOTAL_ logs",
                infoEmpty: "No logs found",
                infoFiltered: "(filtered from _MAX_ total logs)"
            }
        });
    });
</script>
@endsection
