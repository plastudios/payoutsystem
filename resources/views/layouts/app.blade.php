<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Payout')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @stack('styles')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #fdbb2d 0%, #22c1c3 100%);
            --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            --navbar-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        /* Enhanced Navbar */
        .navbar-custom {
            background: var(--primary-gradient);
            height: var(--navbar-height);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            backdrop-filter: blur(10px);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: #ffffff !important;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand i {
            margin-right: 10px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Navigation Links */
        .nav-pills-custom .nav-link {
            background: transparent;
            border: none;
            border-radius: 12px;
            margin: 0 5px;
            padding: 12px 20px;
            color: #ffffff;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .nav-pills-custom .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .nav-pills-custom .nav-link:hover::before {
            left: 100%;
        }

        .nav-pills-custom .nav-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        .nav-pills-custom .nav-link.active {
            background: rgba(255, 255, 255, 0.2) !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            color: #ffffff;
        }

        .nav-pills-custom .nav-link i {
            margin-right: 8px;
            font-size: 1.1rem;
        }

        /* Dropdown Enhancements */
        .dropdown-menu-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
            padding: 15px 0;
            min-width: 250px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-item-custom {
            padding: 12px 25px;
            color: #333;
            font-weight: 500;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.3s ease;
            position: relative;
            text-decoration: none;
        }

        .dropdown-item-custom:hover {
            background: var(--primary-gradient);
            color: white;
            transform: translateX(5px);
        }

        .dropdown-item-custom i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .dropdown-divider-custom {
            margin: 10px 20px;
            border-color: #e1e8ed;
        }

        .dropdown-header-custom {
            padding: 8px 25px;
            font-size: 0.875rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* User Profile Dropdown */
        .user-dropdown {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            padding: 8px 15px;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .user-dropdown:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
            color: white;
            text-decoration: none;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--secondary-gradient);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        /* Role Badges */
        .role-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 8px;
        }

        .role-admin {
            background: var(--secondary-gradient);
            color: white;
        }

        .role-author {
            background: var(--success-gradient);
            color: white;
        }

        .role-checker {
            background: var(--warning-gradient);
            color: white;
        }

        .role-maker {
            background: var(--dark-gradient);
            color: white;
        }

        .role-merchant {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
            color: white;
        }

        /* Content Area */
        .content-wrapper {
            margin-top: var(--navbar-height);
            padding: 30px 0;
            min-height: calc(100vh - var(--navbar-height));
        }

        /* Mobile Responsiveness */
        @media (max-width: 991.98px) {
            .navbar-nav {
                padding: 20px 0;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 15px;
                margin-top: 15px;
            }

            .nav-pills-custom .nav-link {
                margin: 5px 0;
                text-align: center;
            }

            .dropdown-menu-custom {
                position: static !important;
                transform: none !important;
                box-shadow: none;
                background: rgba(255, 255, 255, 0.1);
                margin: 10px 0;
            }
        }

        /* Enhanced Cards */
        .dashboard-card {
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        /* Button Enhancements */
        .btn-rounded {
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-rounded:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Ripple Effect */
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Enhanced Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dashboard">
                <i class="fas fa-wallet"></i> Payout
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                style="color: white;">
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <!-- Main Navigation -->
                <ul class="navbar-nav me-auto nav-pills-custom">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>

                    @if(auth()->check() && in_array(auth()->user()->role, ['checker', 'admin']))
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('payouts/to-check') ? 'active' : '' }}"
                                href="{{ route('payouts.to_check') }}">
                                <i class="fas fa-search"></i> To Check
                            </a>
                        </li>
                    @endif

                    @if(auth()->check() && in_array(auth()->user()->role, ['maker', 'admin']))
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('payouts/to-approve') ? 'active' : '' }}"
                                href="{{ route('payouts.to_approve') }}">
                                <i class="fas fa-check-circle"></i> To Approve
                            </a>
                        </li>
                    @endif

                    @if(auth()->check() && in_array(auth()->user()->role, ['author', 'admin', 'merchant']))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-money-bill-wave"></i> Payout
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom">
                                <li>
                                    <h6 class="dropdown-header dropdown-header-custom"><i class="fas fa-university"></i>
                                        Bank Operations</h6>
                                </li>
                                <li><a class="dropdown-item dropdown-item-custom" href="/payout/create">
                                        <i class="fas fa-plus-circle"></i> Create Payout
                                    </a></li>

                                <li>
                                    <hr class="dropdown-divider dropdown-divider-custom">
                                </li>

                                <li>
                                    <h6 class="dropdown-header dropdown-header-custom"><i class="fas fa-mobile-alt"></i> MFS
                                        Operations</h6>
                                </li>
                                <li><a class="dropdown-item dropdown-item-custom" href="/mfs-payout/upload">
                                        <i class="fas fa-upload"></i> Create Payout (MFS)
                                    </a></li>
                                @if(auth()->check() && in_array(auth()->user()->role, ['author', 'admin']))
                                    <li><a class="dropdown-item dropdown-item-custom" href="/mfs-payout/ExportBatches">
                                            <i class="fas fa-download"></i> Export Batch
                                        </a></li>
                                    <li><a class="dropdown-item dropdown-item-custom" href="/mfs-payout/status-upload">
                                            <i class="fas fa-cloud-upload-alt"></i> Upload Status Details
                                        </a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if(auth()->check() && in_array(auth()->user()->role, ['author', 'admin', 'merchant']))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-chart-line"></i> Reports
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom">
                                <li>
                                    <h6 class="dropdown-header dropdown-header-custom"><i class="fas fa-university"></i>
                                        Bank Reports</h6>
                                </li>
                                <li><a class="dropdown-item dropdown-item-custom" href="/payout/report">
                                        <i class="fas fa-file-alt"></i> Payout Report
                                    </a></li>
                                <li><a class="dropdown-item dropdown-item-custom" href="/payout/details">
                                        <i class="fas fa-list-alt"></i> Payout Details
                                    </a></li>
                                <li><a class="dropdown-item dropdown-item-custom" href="/payout/batches">
                                        <i class="fas fa-layer-group"></i> Batch Details
                                    </a></li>

                                <li>
                                    <hr class="dropdown-divider dropdown-divider-custom">
                                </li>

                                <li>
                                    <h6 class="dropdown-header dropdown-header-custom"><i class="fas fa-mobile-alt"></i> MFS
                                        Reports</h6>
                                </li>
                                <li><a class="dropdown-item dropdown-item-custom" href="/mfs-payout/all">
                                        <i class="fas fa-list"></i> Payout Details
                                    </a></li>
                                @if(auth()->check() && in_array(auth()->user()->role, ['author', 'admin']))
                                    <li><a class="dropdown-item dropdown-item-custom" href="/mfs-payout/payout-summary">
                                            <i class="fas fa-chart-pie"></i> Payout Summary
                                        </a></li>
                                @endif
                                <li><a class="dropdown-item dropdown-item-custom" href="/mfs-payout/batches">
                                        <i class="fas fa-layer-group"></i> Batch Details
                                    </a></li>

                                <li>
                                    <hr class="dropdown-divider dropdown-divider-custom">
                                </li>

                                <li>
                                    <h6 class="dropdown-header dropdown-header-custom"><i class="fas fa-wallet"></i> Balance
                                        Reports</h6>
                                </li>
                                <li><a class="dropdown-item dropdown-item-custom" href="/merchant/balance/details">
                                        <i class="fas fa-chart-bar"></i> Balance Details
                                    </a></li>
                            </ul>
                        </li>
                    @endif

                    @if(auth()->check() && in_array(auth()->user()->role, ['author', 'admin', 'merchant']))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cogs"></i> Settings
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom">
                                @if(auth()->check() && in_array(auth()->user()->role, ['author', 'admin']))
                                    <li><a class="dropdown-item dropdown-item-custom" href="/fi/list">
                                            <i class="fas fa-building"></i> FI List
                                        </a></li>
                                    <li><a class="dropdown-item dropdown-item-custom" href="/merchants">
                                            <i class="fas fa-store"></i> Merchants
                                        </a></li>
                                @endif

                                @if(auth()->check() && auth()->user()->role === 'admin')
                                    <li><a class="dropdown-item dropdown-item-custom" href="/admin/create-user">
                                            <i class="fas fa-user-plus"></i> Create User
                                        </a></li>
                                    <li><a class="dropdown-item dropdown-item-custom" href="/admin/users">
                                            <i class="fas fa-users"></i> User List
                                        </a></li>
                                    <li><a class="dropdown-item dropdown-item-custom" href="{{ route('admin.webhook.logs') }}">
                                            <i class="fas fa-webhook"></i> Webhook Logs
                                        </a></li>
                                @endif

                                @if(auth()->user()->role === 'merchant')
                                    <li><a class="dropdown-item dropdown-item-custom"
                                            href="{{ route('merchant.webhook.edit') }}">
                                            <i class="fas fa-webhook"></i> Webhook Settings
                                        </a></li>
                                    <li><a class="dropdown-item dropdown-item-custom"
                                            href="{{ route('merchant.webhook.logs') }}">
                                            <i class="fas fa-list"></i> Webhook Logs
                                        </a></li>
                                    <li>
                                        <hr class="dropdown-divider dropdown-divider-custom">
                                    </li>
                                    <li><a class="dropdown-item dropdown-item-custom" href="/merchant/balance/details">
                                            <i class="fas fa-info-circle"></i> Balance Details
                                        </a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if(auth()->check() && in_array(auth()->user()->role, ['author', 'admin']))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-wallet"></i> Balance
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom">
                                <li><a class="dropdown-item dropdown-item-custom" href="{{ route('merchant.balance') }}">
                                        <i class="fas fa-coins"></i> Merchant Balance
                                    </a></li>
                                <li><a class="dropdown-item dropdown-item-custom" href="/merchant-balances">
                                        <i class="fas fa-history"></i> Balance History
                                    </a></li>
                            </ul>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/api/documentation') }}" target="_blank">
                            <i class="fas fa-book-open"></i> API Docs
                        </a>
                    </li>
                </ul>

                <!-- User Profile -->
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle user-dropdown" href="#" role="button"
                                data-bs-toggle="dropdown">
                                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                                <span>{{ auth()->user()->name }}</span>
                                <span
                                    class="role-badge role-{{ auth()->user()->role }}">{{ ucfirst(auth()->user()->role) }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                                <li><a class="dropdown-item dropdown-item-custom" href="#"
                                        onclick="alert('Profile page not implemented yet')">
                                        <i class="fas fa-user-circle"></i> Profile
                                    </a></li>
                                <li><a class="dropdown-item dropdown-item-custom" href="#"
                                        onclick="alert('Settings page not implemented yet')">
                                        <i class="fas fa-cog"></i> Account Settings
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider dropdown-divider-custom">
                                </li>
                                <li>
                                    <form method="POST" action="{{ url('/logout') }}" style="margin: 0;">
                                        @csrf
                                        <button class="dropdown-item dropdown-item-custom" type="submit"
                                            style="background: none; border: none; width: 100%; text-align: left;">
                                            <i class="fas fa-sign-out-alt"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        // Enhanced JavaScript functionality
        document.addEventListener('DOMContentLoaded', function () {
            // Dynamic menu highlighting based on current page
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');

            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href && currentPath.includes(href.replace(/^\//, ''))) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });

            // Add ripple effect to nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function (e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255, 255, 255, 0.4);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        pointer-events: none;
                    `;

                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);

                    setTimeout(() => ripple.remove(), 600);
                });
            });

            // Smooth dropdown animations
            document.querySelectorAll('.dropdown').forEach(dropdown => {
                dropdown.addEventListener('show.bs.dropdown', function () {
                    this.querySelector('.dropdown-menu').style.animationName = 'slideDown';
                });
            });
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>

</html>