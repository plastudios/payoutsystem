@extends('layouts.app')

@section('title', 'Merchant Balance Management')

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

    .form-card, .table-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .form-label {
        font-weight: 600;
        color: #374151;
        font-size: 0.875rem;
        margin-bottom: 8px;
    }

    .form-control {
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 0.875rem;
    }

    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn {
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 0.875rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
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
        .form-card, .table-card {
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
        <h2><i class="fas fa-wallet me-3"></i>Manage Merchant Balance</h2>
        <p class="mb-0 opacity-90">Add or deduct balance from merchant accounts</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Form Card -->
    <div class="form-card">
        <h5 class="mb-4"><i class="fas fa-edit me-2"></i>Add Transaction</h5>
        <form action="{{ route('merchant.balance.store') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-2">
                <label class="form-label">Merchant</label>
                <select name="merchant_id" class="form-control" required>
                    <option value="">-- Select Merchant --</option>
                    @foreach($merchants as $merchant)
                        <option value="{{ $merchant->merchant_id }}">{{ $merchant->merchant_id }} ({{ $merchant->company_name }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select name="type" id="balance-type" class="form-control" required>
                    <option value="credit">Credit</option>
                    <option value="debit">Debit</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Amount</label>
                <input type="number" name="amount" step="0.01" class="form-control" required>
            </div>
            <div class="col-md-2" id="charge-field">
                <label class="form-label">Payout Charge (%)</label>
                <input type="number" name="payout_charge" step="0.01" min="0" max="100" class="form-control" value="0" placeholder="e.g. 0.7">
            </div>
            <div class="col-md-2">
                <label class="form-label">Remarks</label>
                <input type="text" name="remarks" class="form-control">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-plus me-1"></i> Submit
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('balance-type').addEventListener('change', function () {
            document.getElementById('charge-field').style.display = this.value === 'credit' ? 'block' : 'none';
        });
    </script>

    <!-- Table Card -->
    <div class="table-card">
        <h5 class="mb-4"><i class="fas fa-chart-bar me-2"></i>Merchant Summary</h5>
        <div class="table-responsive">
            <table class="table table-hover" id="summaryTable">
                <thead>
                    <tr>
                        <th>Merchant ID</th>
                        <th>Company Name</th>
                        <th>Total Credit</th>
                        <th>Total Debit</th>
                        <th>Available Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summary as $row)
                        <tr>
                            <td><code>{{ $row['merchant_id'] }}</code></td>
                            <td><strong>{{ $row['company_name'] }}</strong></td>
                            <td class="text-success"><strong>{{ number_format($row['credit'], 2) }}</strong> <small class="text-muted">BDT</small></td>
                            <td class="text-warning"><strong>{{ number_format($row['debit'], 2) }}</strong> <small class="text-muted">BDT</small></td>
                            <td class="text-success"><strong>{{ number_format($row['available'], 2) }}</strong> <small class="text-muted">BDT</small></td>
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
        $('#summaryTable').DataTable({
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
                search: "Search merchants:",
                lengthMenu: "Show _MENU_ merchants per page",
                info: "Showing _START_ to _END_ of _TOTAL_ merchants",
                infoEmpty: "No merchants found",
                infoFiltered: "(filtered from _MAX_ total merchants)"
            }
        });
    });
</script>
@endsection