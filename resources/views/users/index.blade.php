@extends('layouts.app')
@section('title', 'User List')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h2>User List</h2>
    <a href="{{ route('users.create') }}" class="btn btn-primary">➕ Create User</a>
</div>
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<table class="table table-bordered" id="userTable">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Merchant</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->merchant->company_name ?? '-' }}</td>
            <td>{{ ucfirst($user->role) }}</td>
            <td>
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">✏️ Edit</a>
                <a href="{{ route('users.change_password', $user->id) }}" class="btn btn-sm btn-info">🔑 Password</a>
                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">🗑️ Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#userTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});
</script>
@endpush

