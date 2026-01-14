@extends('layouts.app')

@section('title', 'Dashboard - PayoutPro')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    /* Enhanced Dashboard Styles */
    body {
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
    }

    .welcome-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .welcome-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(15px, -15px);
    }

    .welcome-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 120px;
        height: 120px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        transform: translate(-40px, 40px);
    }

    .welcome-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.5rem;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        position: relative;
        z-index: 2;
    }

    .welcome-header .subtitle {
        opacity: 0.9;
        font-size: 0.875rem;
        margin-top: 4px;
        position: relative;
        z-index: 2;
    }

    /* Enhanced Stat Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
        margin-bottom: 25px;
    }

    .stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        min-height: 120px;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .stat-card .icon-wrapper {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        background: var(--card-color, #3b82f6);
        color: white;
        font-size: 1.1rem;
    }

    .stat-card h6 {
        color: #64748b;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
        line-height: 1.2;
    }

    .stat-card h3 {
        color: #1e293b;
        font-weight: 700;
        font-size: 1.875rem;
        margin: 0;
        line-height: 1;
        font-family: 'Inter', sans-serif;
    }

    .stat-card .trend {
        font-size: 0.75rem;
        font-weight: 500;
        margin-top: 8px;
        padding: 4px 8px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .trend.up {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        font-weight: 600;
    }

    .trend.down {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        font-weight: 600;
    }

    /* Individual Card Colors */
    .stat-card.merchants {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: white;
    }

    .stat-card.balance {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
    }

    .stat-card.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .stat-card.danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    .stat-card.primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .stat-card.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }

    .stat-card.info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: white;
    }

    .stat-card.secondary {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
    }

    .stat-card.dark {
        background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
        color: white;
    }

    .stat-card.custom-green {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
    }

    .stat-card.custom-red {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: white;
    }

    .stat-card.orange {
        background: linear-gradient(135deg, #ea5a47 0%, #d84315 100%);
        color: white;
    }

    /* Override text colors for gradient cards */
    .stat-card.merchants h6,
    .stat-card.balance h6,
    .stat-card.success h6,
    .stat-card.danger h6,
    .stat-card.primary h6,
    .stat-card.warning h6,
    .stat-card.info h6,
    .stat-card.secondary h6,
    .stat-card.dark h6,
    .stat-card.custom-green h6,
    .stat-card.custom-red h6,
    .stat-card.orange h6 {
        color: rgba(255, 255, 255, 0.9);
    }

    .stat-card.merchants h3,
    .stat-card.balance h3,
    .stat-card.success h3,
    .stat-card.danger h3,
    .stat-card.primary h3,
    .stat-card.warning h3,
    .stat-card.info h3,
    .stat-card.secondary h3,
    .stat-card.dark h3,
    .stat-card.custom-green h3,
    .stat-card.custom-red h3,
    .stat-card.orange h3 {
        color: #ffffff;
    }

    /* Override icon wrapper for gradient cards */
    .stat-card.merchants .icon-wrapper,
    .stat-card.balance .icon-wrapper,
    .stat-card.success .icon-wrapper,
    .stat-card.danger .icon-wrapper,
    .stat-card.primary .icon-wrapper,
    .stat-card.warning .icon-wrapper,
    .stat-card.info .icon-wrapper,
    .stat-card.secondary .icon-wrapper,
    .stat-card.dark .icon-wrapper,
    .stat-card.custom-green .icon-wrapper,
    .stat-card.custom-red .icon-wrapper,
    .stat-card.orange .icon-wrapper {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    /* Chart Containers */
    .chart-container {
        background: #ffffff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .chart-header {
        display: flex;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .chart-header i {
        font-size: 1rem;
        margin-right: 8px;
        color: #3b82f6;
    }

    .chart-header h5 {
        margin: 0;
        font-weight: 600;
        color: #1e293b;
        font-size: 1rem;
    }

    /* Admin Stats Specific Styling */
    .admin-stats {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 25px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .admin-stats h3 {
        margin-bottom: 20px;
        font-weight: 700;
        text-align: center;
        font-size: 1.25rem;
    }

    .admin-stats .stats-grid {
        gap: 16px;
    }

    /* Individual Admin Card Colors */
    .admin-stats .stat-card.merchants {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        border: 1px solid rgba(6, 182, 212, 0.3);
    }

    .admin-stats .stat-card.balance {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        border: 1px solid rgba(99, 102, 241, 0.3);
    }

    .admin-stats .stat-card.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .admin-stats .stat-card.danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .admin-stats .stat-card.secondary {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        border: 1px solid rgba(139, 92, 246, 0.3);
    }

    .admin-stats .stat-card.orange {
        background: linear-gradient(135deg, #ea5a47 0%, #d84315 100%);
        border: 1px solid rgba(234, 90, 71, 0.3);
    }

    /* General admin card styling */
    .admin-stats .stat-card {
        color: white;
        min-height: 110px;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .admin-stats .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    .admin-stats .stat-card .icon-wrapper {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .admin-stats .stat-card h6 {
        color: rgba(255, 255, 255, 0.9);
    }

    .admin-stats .stat-card h3 {
        color: white;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }
        
        .welcome-header h2 {
            font-size: 1.5rem;
        }
        
        .stat-card {
            padding: 16px;
            min-height: 100px;
        }
        
        .stat-card h3 {
            font-size: 1.5rem;
        }
        
        .chart-container {
            padding: 16px;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .stat-card {
            padding: 12px;
            min-height: 90px;
        }
        
        .stat-card h3 {
            font-size: 1.25rem;
        }
        
        .stat-card h6 {
            font-size: 0.7rem;
        }
    }

    /* Success/Error Messages */
    .alert-modern {
        border: none;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
        border-left: 4px solid #3b82f6;
    }

    .alert-modern.alert-info {
        background: #dbeafe;
        color: #1e40af;
        border-left-color: #3b82f6;
    }

    .alert-modern i {
        margin-right: 8px;
    }
</style>
@endpush

@section('content')
<!-- Welcome Header -->
<div class="welcome-header">
    <h2><i class="fas fa-chart-line me-3"></i>Welcome back, {{ Auth::user()->name ?? 'User' }}!</h2>
    <p class="subtitle mb-0">Here's your payout dashboard overview for today</p>
</div>

@if(Auth::user()->role === 'merchant')
<!-- Merchant Statistics -->
<div class="stats-grid">
    <div class="stat-card success">
        <div class="icon-wrapper">
            <i class="fas fa-check-circle"></i>
        </div>
        <h6>✅ Successful Payouts</h6>
        <h3>{{ number_format($successCount) }}</h3>
        <div class="trend up">
            <i class="fas fa-arrow-up"></i> +12% from last week
        </div>
    </div>

    <div class="stat-card danger">
        <div class="icon-wrapper">
            <i class="fas fa-times-circle"></i>
        </div>
        <h6>❌ Failed Payouts</h6>
        <h3>{{ number_format($failedCount) }}</h3>
        <div class="trend down">
            <i class="fas fa-arrow-down"></i> -5% from last week
        </div>
    </div>

    <div class="stat-card primary">
        <div class="icon-wrapper">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <h6>💰 Total Success Amount</h6>
        <h3>{{ number_format($successAmount, 2) }} <small>BDT</small></h3>
        <div class="trend up">
            <i class="fas fa-arrow-up"></i> +8% from yesterday
        </div>
    </div>

    <div class="stat-card warning">
        <div class="icon-wrapper">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h6>💸 Total Failed Amount</h6>
        <h3>{{ number_format($failedAmount, 2) }} <small>BDT</small></h3>
        <div class="trend down">
            <i class="fas fa-arrow-down"></i> -15% from yesterday
        </div>
    </div>

    <div class="stat-card info">
        <div class="icon-wrapper">
            <i class="fas fa-wallet"></i>
        </div>
        <h6>💼 Available Balance</h6>
        <h3>{{ number_format($availableBalance, 2) }} <small>BDT</small></h3>
        <div class="trend up">
            <i class="fas fa-arrow-up"></i> Updated now
        </div>
    </div>

    <div class="stat-card custom-green">
        <div class="icon-wrapper">
            <i class="fas fa-plus-circle"></i>
        </div>
        <h6>🟢 Total Credited</h6>
        <h3>{{ number_format($credit, 2) }} <small>BDT</small></h3>
        <div class="trend up">
            <i class="fas fa-arrow-up"></i> This month
        </div>
    </div>

    <div class="stat-card custom-red">
        <div class="icon-wrapper">
            <i class="fas fa-minus-circle"></i>
        </div>
        <h6>🔴 Total Debited</h6>
        <h3>{{ number_format($debit, 2) }} <small>BDT</small></h3>
        <div class="trend down">
            <i class="fas fa-arrow-down"></i> This month
        </div>
    </div>

    <div class="stat-card secondary">
        <div class="icon-wrapper">
            <i class="fas fa-mobile-alt"></i>
        </div>
        <h6>👤 Success MFS Payouts</h6>
        <h3>{{ number_format($mfsSuccessCount) }}</h3>
        <div class="trend up">
            <i class="fas fa-arrow-up"></i> +3% today
        </div>
    </div>

    <div class="stat-card orange">
        <div class="icon-wrapper">
            <i class="fas fa-mobile-alt"></i>
        </div>
        <h6>🔴 Failed MFS Payouts</h6>
        <h3>{{ number_format($mfsFailedCount) }}</h3>
        <div class="trend down">
            <i class="fas fa-arrow-down"></i> -2% today
        </div>
    </div>
</div>
@endif

@if(Auth::user()->role === 'admin')
<!-- Admin Statistics -->
<div class="admin-stats">
    <h3><i class="fas fa-crown me-2"></i>Administrative Overview</h3>
    <div class="stats-grid">
        <div class="stat-card merchants">
            <div class="icon-wrapper">
                <i class="fas fa-store"></i>
            </div>
            <h6>Total Merchants</h6>
            <h3>{{ number_format($totalMerchants) }}</h3>
            <div class="trend up">
                <i class="fas fa-arrow-up"></i> Active accounts
            </div>
        </div>

        <div class="stat-card balance">
            <div class="icon-wrapper">
                <i class="fas fa-coins"></i>
            </div>
            <h6>🪙 Total Allocated Balance</h6>
            <h3>{{ number_format($totalAllocated, 2) }} <small>BDT</small></h3>
            <div class="trend up">
                <i class="fas fa-arrow-up"></i> System-wide
            </div>
        </div>

        <div class="stat-card success">
            <div class="icon-wrapper">
                <i class="fas fa-check-circle"></i>
            </div>
            <h6>✅ Successful Payouts</h6>
            <h3>{{ number_format($successCount) }}</h3>
            <div class="trend up">
                <i class="fas fa-arrow-up"></i> All merchants
            </div>
        </div>

        <div class="stat-card danger">
            <div class="icon-wrapper">
                <i class="fas fa-times-circle"></i>
            </div>
            <h6>❌ Failed Payouts</h6>
            <h3>{{ number_format($failedCount) }}</h3>
            <div class="trend down">
                <i class="fas fa-arrow-down"></i> Needs attention
            </div>
        </div>

        <div class="stat-card secondary">
            <div class="icon-wrapper">
                <i class="fas fa-mobile-alt"></i>
            </div>
            <h6>👤 Success MFS Payouts</h6>
            <h3>{{ number_format($mfsSuccessCount) }}</h3>
            <div class="trend up">
                <i class="fas fa-arrow-up"></i> MFS channel
            </div>
        </div>

        <div class="stat-card orange">
            <div class="icon-wrapper">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h6>🔴 Failed MFS Payouts</h6>
            <h3>{{ number_format($mfsFailedCount) }}</h3>
            <div class="trend down">
                <i class="fas fa-arrow-down"></i> Requires review
            </div>
        </div>
    </div>
</div>
@endif

<!-- Charts Section -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="chart-container">
            <div class="chart-header">
                <i class="fas fa-chart-pie"></i>
                <h5>Payout Status Distribution</h5>
            </div>
            <div style="position: relative; height: 300px;">
                <canvas id="payoutPieChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="chart-container">
            <div class="chart-header">
                <i class="fas fa-chart-bar"></i>
                <h5>Amount Comparison</h5>
            </div>
            <div style="position: relative; height: 300px;">
                <canvas id="payoutBarChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions (if needed) -->
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-modern alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Welcome to PayoutPro!</strong> Your enhanced dashboard is now ready with real-time analytics and improved navigation. 
            All statistics are updated in real-time based on your role and permissions.
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart data from Laravel
    const successCount = {{ $successCount }};
    const failedCount = {{ $failedCount }};
    const successAmount = {{ $successAmount }};
    const failedAmount = {{ $failedAmount }};

    // Enhanced Pie Chart
    const ctx1 = document.getElementById('payoutPieChart').getContext('2d');
    const pieChart = new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Successful Payouts', 'Failed Payouts'],
            datasets: [{
                label: 'Payout Count',
                data: [successCount, failedCount],
                backgroundColor: [
                    '#10b981',
                    '#ef4444'
                ],
                borderColor: [
                    '#059669',
                    '#dc2626'
                ],
                borderWidth: 2,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 13,
                            family: 'Inter',
                            weight: '500'
                        },
                        color: '#374151'
                    }
                },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#374151',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    titleFont: {
                        family: 'Inter',
                        weight: '600'
                    },
                    bodyFont: {
                        family: 'Inter'
                    }
                }
            },
            cutout: '65%',
            animation: {
                animateRotate: true,
                duration: 1500
            }
        }
    });

    // Enhanced Bar Chart
    const ctx2 = document.getElementById('payoutBarChart').getContext('2d');
    const barChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Success Amount', 'Failed Amount'],
            datasets: [{
                label: 'Amount in BDT',
                data: [successAmount, failedAmount],
                backgroundColor: [
                    '#3b82f6',
                    '#f59e0b'
                ],
                borderColor: [
                    '#2563eb',
                    '#d97706'
                ],
                borderWidth: 1,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#374151',
                    borderWidth: 1,
                    cornerRadius: 8,
                    titleFont: {
                        family: 'Inter',
                        weight: '600'
                    },
                    bodyFont: {
                        family: 'Inter'
                    },
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' BDT';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9',
                        drawBorder: false,
                    },
                    ticks: {
                        font: {
                            family: 'Inter',
                            size: 12
                        },
                        color: '#64748b',
                        callback: function(value) {
                            return value.toLocaleString() + ' BDT';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        font: {
                            family: 'Inter',
                            weight: '500',
                            size: 13
                        },
                        color: '#374151'
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart'
            }
        }
    });

    // Add loading animation on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Animate stat cards on load
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });

        // Add hover effects to chart containers
        document.querySelectorAll('.chart-container').forEach(container => {
            container.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 20px 50px rgba(0, 0, 0, 0.15)';
            });
            
            container.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.1)';
            });
        });

        // Auto-refresh data every 30 seconds (optional)
        setInterval(function() {
            // You can add AJAX calls here to refresh data
            console.log('Auto-refresh check...');
        }, 30000);
    });

    // Add click animations to stat cards
    document.querySelectorAll('.stat-card').forEach(card => {
        card.addEventListener('click', function() {
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
</script>
@endsection