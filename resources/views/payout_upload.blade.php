@extends('layouts.app')

@section('title', 'Create Payout')

@section('content')
<div class="container">
    <h2>Upload Payout Excel</h2>

    {{-- Sample Excel Download --}}
    <a href="{{ asset('storage/payout_sample.xlsx') }}" class="btn btn-outline-secondary mb-3" download>
        📥 Download Sample Excel
    </a>

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Error message --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Form --}}
    <form action="{{ url('/payout/upload') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Merchant ID dropdown --}}
        @unless(auth()->user()->role === 'merchant')
        <div class="mb-3">
            <label for="merchant_id" class="form-label">Select Merchant</label>
            <select name="merchant_id" id="merchant_id" class="form-control" required>
                <option value="">-- Select Merchant --</option>
                @foreach($merchants as $merchant)
                    <option value="{{ $merchant->merchant_id }}">
                        {{ $merchant->merchant_id }} ({{ $merchant->company_name }})
                    </option>
                @endforeach
            </select>
        </div>
        @else
        {{-- Pass merchant_id as hidden input --}}
        <input type="hidden" name="merchant_id" value="{{ auth()->user()->merchant_id }}">
        @endunless
        {{-- Excel upload --}}
        <div class="mb-3">
            <label for="payout_file" class="form-label">Select Excel File</label>
            <input type="file" name="payout_file" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Upload & Process</button>
    </form>
</div>
@endsection
