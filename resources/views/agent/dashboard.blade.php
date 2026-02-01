@extends('layouts.app')

@section('title', 'Agent Dashboard')

@push('styles')
<style>
    .agent-dashboard .welcome-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 16px;
        padding: 28px;
        margin-bottom: 24px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }
    .agent-dashboard .welcome-card h2 { font-weight: 700; margin-bottom: 8px; }
    .agent-dashboard .welcome-card .subtitle { opacity: 0.9; font-size: 0.95rem; }
    .agent-dashboard .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }
    .agent-dashboard .quick-action-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .agent-dashboard .quick-action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.2);
        border-color: #667eea;
    }
    .agent-dashboard .quick-action-card .icon {
        width: 48px;
        height: 48px;
        margin: 0 auto 12px;
        border-radius: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .agent-dashboard .quick-action-card h5 { font-weight: 600; margin-bottom: 4px; font-size: 1rem; }
    .agent-dashboard .quick-action-card p { margin: 0; font-size: 0.8rem; color: #64748b; }
    .agent-dashboard .placeholder-section {
        background: #f8fafc;
        border-radius: 12px;
        padding: 24px;
        margin-top: 24px;
        border: 1px dashed #cbd5e1;
        color: #64748b;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="agent-dashboard">
    <div class="welcome-card">
        <h2><i class="fas fa-user-tie me-2"></i>Agent Dashboard</h2>
        <p class="subtitle">Welcome back, {{ auth()->user()->name }}. Manage payment requests and reports from here.</p>
    </div>

    <div class="quick-actions">
        <a href="{{ route('agent.payment_requests.index') }}" class="quick-action-card">
            <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
            <h5>Payment Request</h5>
            <p>Mark payouts as Success or Failed</p>
        </a>
    </div>

    <div class="placeholder-section">
        <i class="fas fa-info-circle fa-2x mb-2"></i>
        <p class="mb-0">This is your agent dashboard. Use the link above or the menu to access Payment Request.</p>
    </div>
</div>
@endsection
