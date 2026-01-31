@extends('layouts.app')

@section('title', 'Create Payout')

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
        color: #3b82f6;
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

    .form-control,
    .form-select {
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn-download {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        color: #374151;
        border: 1.5px solid #cbd5e1;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }

    .btn-download:hover {
        background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        color: #1e293b;
    }

    .btn-download i {
        margin-right: 8px;
    }

    .btn-upload {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: none;
        font-weight: 600;
        padding: 12px 28px;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .btn-upload:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        color: white;
    }

    .btn-upload i {
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

        .btn-download,
        .btn-upload {
            width: 100%;
            justify-content: center;
        }

        .info-box {
            font-size: 0.8rem;
            padding: 12px;
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
        <h2><i class="fas fa-cloud-upload-alt me-3"></i>Upload Payout Excel</h2>
        <p>Upload an Excel file with payout details to create batch transactions</p>
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

    <!-- Download Template -->
    <div class="mb-4">
        <a href="{{ asset('storage/payout_sample.xlsx') }}" class="btn-download" download>
            <i class="fas fa-download"></i>
            Download Sample Excel Template
        </a>
    </div>

    <!-- Upload Form Card -->
    <div class="form-card">
        <h5><i class="fas fa-file-excel"></i>Payout Upload Form</h5>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <p>Please ensure your Excel file follows the template format. All required fields must be filled correctly.</p>
        </div>

        <form action="{{ url('/payout/upload') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @unless(auth()->user()->role === 'merchant')
            <div class="mb-3">
                <label for="merchant_id" class="form-label">
                    <i class="fas fa-store"></i>Select Merchant
                </label>
                <select name="merchant_id" id="merchant_id" class="form-select" required>
                    <option value="">-- Select Merchant --</option>
                    @foreach($merchants as $merchant)
                        <option value="{{ $merchant->merchant_id }}">
                            {{ $merchant->merchant_id }} ({{ $merchant->company_name }})
                        </option>
                    @endforeach
                </select>
            </div>
            @else
            <input type="hidden" name="merchant_id" value="{{ auth()->user()->merchant_id }}">
            @endunless

            <div class="mb-4">
                <label for="payout_file" class="form-label">
                    <i class="fas fa-file-upload"></i>Select Excel File
                </label>
                <input type="file" name="payout_file" id="payout_file" class="form-control" required accept=".xlsx,.xls">
                <small class="text-muted">Accepted formats: .xlsx, .xls</small>
            </div>

            <button type="submit" class="btn-upload">
                <i class="fas fa-upload"></i>
                Upload & Process
            </button>
        </form>
    </div>
</div>
@endsection
