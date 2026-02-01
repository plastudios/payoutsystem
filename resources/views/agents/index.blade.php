@extends('layouts.app')
@section('title', 'Agents')
@section('content')
<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Agents</h2>
        <a href="{{ route('agents.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Create Agent
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="agentTable">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Merchants</th>
                    <th width="200">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agents as $agent)
                <tr>
                    <td>{{ $agent->name }}</td>
                    <td>{{ $agent->email }}</td>
                    <td>{{ $agent->phone ?? '—' }}</td>
                    <td>
                        @if($agent->merchants->count() > 0)
                            {{ $agent->merchants->pluck('company_name')->join(', ') }}
                            <span class="text-muted">({{ $agent->merchants->count() }})</span>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('agents.edit', $agent->id) }}" class="btn btn-sm btn-warning" title="Edit Agent">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('agents.destroy', $agent->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this agent?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit" title="Delete Agent">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No agents yet. <a href="{{ route('agents.create') }}">Create one</a>.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    @if($agents->count() > 0)
    $('#agentTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        order: [[0, 'asc']],
        pageLength: 25,
        language: {
            search: "Search agents:",
            lengthMenu: "Show _MENU_ agents per page",
            info: "Showing _START_ to _END_ of _TOTAL_ agents",
            infoEmpty: "No agents found",
            infoFiltered: "(filtered from _MAX_ total agents)"
        }
    });
    @endif
});
</script>
@endpush
