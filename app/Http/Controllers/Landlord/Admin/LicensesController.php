<?php

namespace App\Http\Controllers\Landlord\Admin;

use App\Models\Landlord\Account;
use App\Models\Landlord\License;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LicensesController extends Controller
{
    public function create(Account $account)
    {
        return view('landlord.admin.licenses.create', compact('account'));
    }

    public function store(Request $request, Account $account)
    {
        $data = $request->validate([
            'starts_at' => ['required', 'date'],
        ]);

        $startsAt = CarbonImmutable::parse($data['starts_at']);
        $expiresAt = $startsAt->addMonthsNoOverflow(12);

        License::create([
            'account_id' => $account->id,
            'plan_code' => 'standard_100',
            'plan_name' => 'Standard 100',
            'max_tenants' => 100,
            'starts_at' => $data['starts_at'],
            'expires_at' => $expiresAt->toDateTimeString(),
            'status' => 'active',
            'grace_days' => 5,
        ]);

        return redirect("/admin/accounts/{$account->id}")->with('ok', 'Licencia creada');
    }
}
