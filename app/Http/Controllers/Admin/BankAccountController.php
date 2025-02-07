<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BankAccountController extends Controller
{
    public function index()
    {
        $bankAccounts = BankAccount::orderBy('sort_order')->get();
        return response()->json($bankAccounts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'branch' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('public/banks');
            $validated['logo'] = Storage::url($path);
        }

        $bankAccount = BankAccount::create($validated);
        return response()->json($bankAccount, 201);
    }

    // Thêm các methods khác...
} 