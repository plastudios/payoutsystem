@extends('layouts.app')
@section('title', 'Change Password')
@section('content')
<h2>Change User Password</h2>
<form action="{{ route('users.update_password', $user->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label>New Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    <button class="btn btn-primary">Update Password</button>
</form>

@endsection