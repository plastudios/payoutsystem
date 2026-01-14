@extends('layouts.app')

@section('title', 'Webhook Settings')

@section('content')
<div class="container">
    <h2 class="mb-4">Webhook Settings</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('merchant.webhook.update') }}">
        @csrf
        <div class="mb-3">
            <label for="webhook_url" class="form-label">Webhook URL</label>
            <input type="url" class="form-control" id="webhook_url" name="webhook_url" value="{{ old('webhook_url', $merchant->webhook_url) }}" placeholder="https://example.com/your-webhook">
        </div>
        <button type="submit" class="btn btn-primary">Update Webhook URL</button>
    </form>
</div>
@endsection
