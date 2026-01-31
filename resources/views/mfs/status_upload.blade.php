@extends('layouts.app')

@section('title', 'Update MFS Payout Status')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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

    .alert {
        border-radius: 12px;
        border: none;
        padding: 16px 20px;
        margin-bottom: 24px;
        font-weight: 500;
    }

    .alert-success {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
    }

    .alert-danger {
        background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
        color: #dc2626;
    }

    .form-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
    }

    .form-card h5 {
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        font-size: 1.1rem;
    }

    .form-card h5 i {
        color: #10b981;
        margin-right: 10px;
    }

    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        font-size: 0.875rem;
    }

    .form-label i {
        margin-right: 6px;
        color: #6b7280;
    }

    .form-control {
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .btn-update {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        font-weight: 600;
        padding: 12px 28px;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .btn-update:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        color: white;
    }

    .btn-update i {
        margin-right: 8px;
    }

    .info-box {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border: 1.5px solid #93c5fd;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
    }

    .info-box i {
        color: #1e40af;
        margin-right: 8px;
    }

    .info-box p {
        margin: 0;
        color: #1e40af;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .info-box ul {
        margin: 8px 0 0 24px;
        padding: 0;
        color: #1e40af;
        font-size: 0.85rem;
    }

    .info-box ul li {
        margin-bottom: 4px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-card {
            padding: 20px;
        }
        
        .page-header {
            padding: 16px;
        }
        
        .page-header h2 {
            font-size: 1.5rem;
        }

        .page-header p {
            font-size: 0.8rem;
        }

        .form-card h5 {
            font-size: 1rem;
        }

        .alert {
            padding: 12px 16px;
            font-size: 0.85rem;
        }

        .btn-update {
            width: 100%;
            justify-content: center;
        }

        .info-box {
            font-size: 0.8rem;
            padding: 12px;
        }

        .info-box ul {
            font-size: 0.75rem;
        }
    }

    @media (max-width: 576px) {
        .page-header h2 {
            font-size: 1.25rem;
        }

        .form-card {
            padding: 16px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h2><i class="fas fa-sync-alt me-3"></i>Upload Processed Status File</h2>
        <p>Update MFS payout transaction statuses by uploading the processed Excel file</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Upload Form Card -->
    <div class="form-card">
        <h5><i class="fas fa-file-upload"></i>Status Update Form</h5>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <p><strong>Important:</strong> The status file must contain the following columns:</p>
            <ul>
                <li>Transaction ID or Reference Number</li>
                <li>Updated Status (Success/Failed/Pending)</li>
                <li>Any additional processing information</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('mfs.status.update') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label for="status_file" class="form-label">
                    <i class="fas fa-file-excel"></i>Select Status Excel File
                </label>
                <input type="file" class="form-control" name="status_file" id="status_file" required accept=".xlsx,.xls">
                <small class="text-muted">Accepted formats: .xlsx, .xls</small>
            </div>

            <button type="submit" class="btn-update">
                <i class="fas fa-check-circle"></i>
                Update Status
            </button>
        </form>
    </div>
</div>
@endsection
