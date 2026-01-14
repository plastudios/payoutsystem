<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\FinancialInstitution;

class FIController extends Controller
{
    public function fetchAndStoreFi()
    {
        $response = Http::post('https://sandbox.aamarpay.com/mghnabank/fi_list.php', [
            'token' => 'eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiI5OWQzZmExYWVmNmM0NTliYmUzZDI2NGMyMmZiMWNjYS4zNDc4IiwiZXhwIjoxNzQ1OTk0NzQ2LCJpYXQiOjE3NDU5OTExNDZ9.CWYMsT5TORb31IHv-OItOaiycZVti3uC5K39PCOvyb4',
            'referenceKey' => 'AP-23984729837498',
            'fiCode' => ''
        ]);

        $data = $response->json();

        if (isset($data['fiList'])) {
            foreach ($data['fiList'] as $fi) {
                FinancialInstitution::updateOrCreate(
                    ['fiCode' => $fi['fiCode']],
                    [
                        'fiType' => $fi['fiType'],
                        'fiName' => $fi['fiName'],
                        'fiShortCod' => $fi['fiShortCod'],
                        'fiStatus' => $fi['fiStatus'],
                        'cardRoutingNo' => $fi['cardRoutingNo']
                    ]
                );
            }
        }

        return redirect('/fi/list')->with('success', 'FI List updated successfully.');
    }
    public function showFiList()
    {
        $fiList = FinancialInstitution::all();
        return view('fi_list', compact('fiList'));
    }
}
