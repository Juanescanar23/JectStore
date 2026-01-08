<?php

namespace App\Http\Controllers\Landlord\Admin;

use App\Models\Landlord\Plan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PlansController extends Controller
{
    public function index()
    {
        $plans = Plan::query()->orderByDesc('id')->get();

        return view('landlord.admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('landlord.admin.plans.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Plan::create($data);

        return redirect('/admin/plans')->with('ok', 'Plan creado');
    }

    public function edit(Plan $plan)
    {
        return view('landlord.admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $this->validated($request, $plan->id);

        $plan->update($data);

        return redirect('/admin/plans')->with('ok', 'Plan actualizado');
    }

    private function validated(Request $request, ?int $planId = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50'],
            'price_usd' => ['required', 'numeric', 'min:0.01'],
            'contract_months' => ['required', 'integer', 'min:1'],
            'grace_days' => ['required', 'integer', 'min:0'],
            'max_tenants' => ['required', 'integer', 'min:1'],
            'features' => ['nullable', 'string'],
        ];

        if ($planId) {
            $rules['code'][] = Rule::unique('landlord.plans', 'code')->ignore($planId);
        } else {
            $rules['code'][] = Rule::unique('landlord.plans', 'code');
        }

        $data = $request->validate($rules);

        $featuresRaw = trim((string) ($data['features'] ?? ''));
        if ($featuresRaw === '') {
            $data['features'] = null;
        } else {
            $decoded = json_decode($featuresRaw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw ValidationException::withMessages([
                    'features' => 'Features debe ser un JSON valido.',
                ]);
            }
            $data['features'] = $decoded;
        }

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
