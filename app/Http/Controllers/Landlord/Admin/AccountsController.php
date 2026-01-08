<?php

namespace App\Http\Controllers\Landlord\Admin;

use App\Models\Landlord\Account;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AccountsController extends Controller
{
    public function index()
    {
        $accounts = Account::query()->latest()->get();
        return view('landlord.admin.accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('landlord.admin.accounts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'billing_email' => ['nullable', 'email', 'max:255'],
        ]);

        $account = Account::create([
            'name' => $data['name'],
            'billing_email' => $data['billing_email'] ?? null,
            'status' => 'active',
        ]);

        return redirect("/admin/accounts/{$account->id}")->with('ok', 'Account creado');
    }

    public function show(Account $account)
    {
        $licenses = $account->licenses()->with('plan')->latest()->get();
        $users = $account->users()->latest()->get();

        return view('landlord.admin.accounts.show', compact('account', 'licenses', 'users'));
    }
}
