<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'Payout')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/favicon.ico') }}">
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="/dashboard">Payout</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
            <a class="nav-link" href="/dashboard">Dashboard</a>
        </li>

        @if(auth()->check() && in_array(auth()->user()->role, ['checker', 'admin']))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('payouts.to_check') }}">🕵️ To Check</a>
        </li>
        @endif

        @if(auth()->check() && in_array(auth()->user()->role, ['maker', 'admin']))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('payouts.to_approve') }}">✅ To Approve</a>
        </li>
        @endif

        @if(auth()->check() && in_array(auth()->user()->role, ['author', 'admin']))
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="payoutDropdownAll" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Payout
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/payout/create">Create Payout</a></li>
                <li><a class="dropdown-item" href="/payout/details">Payout Details</a></li>
            </ul>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="reportDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Report
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/payout/report">Payout Report</a></li>
                <li><a class="dropdown-item" href="/payout/batches">Payout Batch Details</a></li>
            </ul>
        </li>
        @endif
        @if(auth()->check() && in_array(auth()->user()->role, ['merchant']))
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="reportDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Report
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/payout/create">Create Payout</a></li>
                <li><a class="dropdown-item" href="/payout/report">Payout Report</a></li>
                <li><a class="dropdown-item" href="/payout/details">Payout Details</a></li>
                <li><a class="dropdown-item" href="/payout/batches">Payout Batch Details</a></li>
                <li><a class="dropdown-item" href="/merchant/balance/details">Balance Details</a></li>
            </ul>
        </li>
        @endif
        <li class="nav-item dropdown">
        @if(auth()->check() && in_array(auth()->user()->role, ['author', 'admin']))
            <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Settings
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/fi/list">FI List</a></li>
                <li><a class="dropdown-item" href="/merchants">Merchants</a></li>
                @if(auth()->check() && in_array(auth()->user()->role, ['admin']))
                <li><a class="dropdown-item" href="/admin/create-user">Create Merchant User</a></li>
                <li><a class="dropdown-item" href="/admin/users">User List</a></li>
                @endif
            </ul>
            @endif
        </li>

        <li class="nav-item dropdown">
        @if(auth()->check() && in_array(auth()->user()->role, ['author', 'admin']))
            <a class="nav-link dropdown-toggle" href="#" id="balanceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Balance
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('merchant.balance') }}">Merchant Balance</a></li>
            </ul>
        @endif
        </li>
      </ul>
        
      <ul class="navbar-nav ms-auto">
        @auth
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <form method="POST" action="{{ url('/logout') }}">
                        @csrf
                        <button class="dropdown-item" type="submit">Logout</button>
                    </form>
                </li>
            </ul>
        </li>
        @endauth
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
    @yield('content')
</div>

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

@yield('scripts')
@stack('scripts')
</body>
</html>
