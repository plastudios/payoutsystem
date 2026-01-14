<?php

namespace App\Http\Controllers;
use App\Models\Merchant;

use Illuminate\Http\Request;

class MerchantController extends Controller
{
    public function index()
    {
        $merchants = Merchant::all();
        return view('merchants.index', compact('merchants'));
    }

    public function create()
    {
        return view('merchants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'merchant_id' => 'required|unique:merchants',
            'email' => 'required|email|unique:merchants',
            'name' => 'required',
            'company_name' => 'required',
            'status' => 'required|in:active,inactive',
        ]);

        Merchant::create($request->all());

        return redirect('/merchants')->with('success', 'Merchant added successfully!');
    }
    
}
