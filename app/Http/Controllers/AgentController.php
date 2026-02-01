<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AgentController extends Controller
{
    public function index()
    {
        $agents = User::where('role', 'agent')->with('merchants')->get();
        return view('agents.index', compact('agents'));
    }

    public function create()
    {
        $merchants = Merchant::all();
        return view('agents.create', compact('merchants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:50',
            'password' => 'required|min:6|confirmed',
            'merchant_ids' => 'required|array|min:1',
            'merchant_ids.*' => 'required|exists:merchants,merchant_id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'agent',
            'merchant_id' => null,
        ]);

        $user->merchants()->sync($request->merchant_ids);

        return redirect()->route('agents.index')->with('success', 'Agent created successfully.');
    }

    public function edit($id)
    {
        $agent = User::where('role', 'agent')->with('merchants')->findOrFail($id);
        $merchants = Merchant::all();
        return view('agents.edit', compact('agent', 'merchants'));
    }

    public function update(Request $request, $id)
    {
        $agent = User::where('role', 'agent')->findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $agent->id,
            'phone' => 'required|string|max:50',
            'merchant_ids' => 'required|array|min:1',
            'merchant_ids.*' => 'required|exists:merchants,merchant_id',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|min:6|confirmed';
        }

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'merchant_id' => null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $agent->update($data);
        $agent->merchants()->sync($request->merchant_ids);

        return redirect()->route('agents.index')->with('success', 'Agent updated successfully.');
    }

    public function destroy($id)
    {
        $agent = User::where('role', 'agent')->findOrFail($id);
        $agent->merchants()->sync([]);
        $agent->delete();

        return redirect()->route('agents.index')->with('success', 'Agent deleted successfully.');
    }
}
