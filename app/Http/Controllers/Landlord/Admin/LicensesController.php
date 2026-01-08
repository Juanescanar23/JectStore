<?php

namespace App\Http\Controllers\Landlord\Admin;

use App\Models\Landlord\Account;
use App\Models\Landlord\License;
use App\Models\Landlord\Plan;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class LicensesController extends Controller
{
    public function create(Account $account)
    {
        $plans = Plan::query()
            ->where('is_active', true)
            ->orderBy('price_usd')
            ->get();

        return view('landlord.admin.licenses.create', compact('account', 'plans'));
    }

    public function store(Request $request, Account $account)
    {
        $data = $request->validate([
            'plan_id' => [
                'required',
                Rule::exists('landlord.plans', 'id')->where('is_active', true),
            ],
            'starts_at' => ['required', 'date'],
        ]);

        $plan = Plan::query()->findOrFail($data['plan_id']);
        $startsAt = CarbonImmutable::parse($data['starts_at']);
        $expiresAt = $startsAt->addMonthsNoOverflow((int) $plan->contract_months);

        License::create([
            'account_id' => $account->id,
            'plan_id' => $plan->id,
            'plan_code' => $plan->code,
            'plan_name' => $plan->name,
            'max_tenants' => $plan->max_tenants,
            'amount' => $plan->price_usd,
            'price_usd' => $plan->price_usd,
            'currency' => 'USD',
            'contract_months' => $plan->contract_months,
            'grace_days' => $plan->grace_days,
            'starts_at' => $startsAt->toDateTimeString(),
            'expires_at' => $expiresAt->toDateTimeString(),
            'status' => 'active',
        ]);

        return redirect("/admin/accounts/{$account->id}")->with('ok', 'Licencia creada');
    }

    public function edit(Account $account, License $license)
    {
        $this->ensureLicenseAccount($account, $license);

        $plans = Plan::query()->orderBy('price_usd')->get();

        return view('landlord.admin.licenses.edit', compact('account', 'license', 'plans'));
    }

    public function update(Request $request, Account $account, License $license)
    {
        $this->ensureLicenseAccount($account, $license);

        $data = $request->validate([
            'plan_id' => ['required', 'exists:landlord.plans,id'],
            'starts_at' => ['required', 'date'],
            'price_usd' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'contract_months' => ['required', 'integer', 'min:1'],
            'grace_days' => ['required', 'integer', 'min:0'],
            'max_tenants' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:active,grace,suspended,expired,cancelled'],
        ]);

        $plan = Plan::query()->findOrFail($data['plan_id']);
        $startsAt = CarbonImmutable::parse($data['starts_at']);
        $expiresAt = $startsAt->addMonthsNoOverflow((int) $data['contract_months']);
        $currency = strtoupper((string) $data['currency']);

        $license->fill([
            'plan_id' => $plan->id,
            'plan_code' => $plan->code,
            'plan_name' => $plan->name,
            'max_tenants' => (int) $data['max_tenants'],
            'amount' => (float) $data['price_usd'],
            'price_usd' => (float) $data['price_usd'],
            'currency' => $currency,
            'contract_months' => (int) $data['contract_months'],
            'grace_days' => (int) $data['grace_days'],
            'starts_at' => $startsAt->toDateTimeString(),
            'expires_at' => $expiresAt->toDateTimeString(),
            'status' => $data['status'],
        ]);

        $license->save();

        return redirect("/admin/accounts/{$account->id}")->with('ok', 'Licencia actualizada');
    }

    private function ensureLicenseAccount(Account $account, License $license): void
    {
        if ($license->account_id !== $account->id) {
            abort(404);
        }
    }
}
