@extends('layouts.app')

@section('title', 'Upload MFS Payouts')

@section('content')
<div class="container">
    <h2 class="mb-4">📤 Upload MFS Payout Sheet</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <a href="{{ asset('storage/mfs_payout_template.xlsx') }}" class="btn btn-sm btn-outline-info mb-3">
        ⬇️ Download Excel Template
    </a>

    <form method="POST" action="{{ route('mfs.upload.process') }}" enctype="multipart/form-data" class="mb-4">
        @csrf
<!-- 
        <div class="mb-3">
            <label for="merchant_id" class="form-label">Select Merchant</label>
            <select name="merchant_id" class="form-select" required>
                <option value="">-- Choose Merchant --</option>
                @foreach($merchants as $merchant)
                    <option value="{{ $merchant->merchant_id }}">{{ $merchant->merchant_id }}</option>
                @endforeach
            </select>
        </div> -->

        @php
        $isMerchant = auth()->user()->role === 'merchant';
        @endphp

        @if(!$isMerchant)
            <div class="mb-3">
                <label for="merchant_id" class="form-label">Select Merchant</label>
                <select name="merchant_id" class="form-select" required>
                    <option value="">-- Choose Merchant --</option>
                    @foreach($merchants as $merchant)
                        <option value="{{ $merchant->merchant_id }}">{{ $merchant->merchant_id }}</option>
                    @endforeach
                </select>
            </div>
        @else
            <input type="hidden" name="merchant_id" value="{{ auth()->user()->merchant_id }}">
        @endif

        <div class="mb-3">
            <label for="payout_file" class="form-label">Select Excel File</label>
            <input type="file" class="form-control" name="payout_file" required accept=".xlsx,.xls">
        </div>

        <button class="btn btn-primary">Upload</button>
    </form>
</div>
@endsection
