<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('merchant')->get(); // assuming User has a merchant_id relationship
        return view('admin.user_list', compact('users'));
    }
    // Show form
    public function create()
    {
        $merchants = Merchant::all();
        return view('admin.create_user', compact('merchants'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $merchants = Merchant::all();

        return view('users.edit', compact('user', 'merchants'));
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'merchant_id' => 'required|exists:merchants,merchant_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,author,checker,maker,merchant'
        ]);

        $user->update([
            'merchant_id' => $request->merchant_id,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
    public function showChangePasswordForm($id)
    {
        $user = User::findOrFail($id);
        return view('users.change_password', compact('user'));
    }
    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'Password updated successfully.');
    }

    // Handle form submission
    public function store(Request $request)
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,merchant_id',
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'merchant_id' => $request->merchant_id,
            'email' => $request->email,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'role' => 'merchant',
        ]);

        return back()->with('success', 'User created successfully.');
    }

    public function user_index()
    {
        // Exclude agents; they are managed from the Agents section
        $users = User::where('role', '!=', 'agent')->with('merchant')->get();
        return view('users.index', compact('users'));
    }

    public function user_create()
    {
        $merchants = Merchant::all();
        return view('users.create', compact('merchants'));
    }
    public function user_store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'merchant_id' => 'required|exists:merchants,merchant_id',
            'role' => 'required|in:admin,author,checker,maker,merchant'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'merchant_id' => $request->merchant_id,
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }
}
