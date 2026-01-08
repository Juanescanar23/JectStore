<?php

namespace App\Http\Controllers\Landlord\Admin;

use App\Models\Landlord\Account;
use App\Models\Landlord\LandlordUser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

class AccountUsersController extends Controller
{
    public function create(Account $account)
    {
        return view('landlord.admin.users.create', compact('account'));
    }

    public function store(Request $request, Account $account)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:landlord.landlord_users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:account_owner,account_manager'],
        ]);

        LandlordUser::create([
            'account_id' => $account->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'status' => 'active',
        ]);

        return redirect("/admin/accounts/{$account->id}")->with('ok', 'Usuario creado');
    }
}
