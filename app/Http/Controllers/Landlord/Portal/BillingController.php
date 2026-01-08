<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landlord\Portal;

use App\Http\Controllers\Controller;
use App\Models\Landlord\License;
use App\Models\Landlord\LicenseBilling;
use App\Services\Billing\DLocalGo\LicenseCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class BillingController extends Controller
{
    public function status()
    {
        $user = Auth::guard('landlord')->user();
        $license = License::query()
            ->where('account_id', $user->account_id)
            ->orderByDesc('expires_at')
            ->firstOrFail();

        $billing = LicenseBilling::query()
            ->where('license_id', $license->id)
            ->where('provider', 'dlocalgo')
            ->first();

        return response()->json([
            'license' => [
                'id' => $license->id,
                'status' => $license->status,
                'starts_at' => $license->starts_at,
                'expires_at' => $license->expires_at,
                'grace_days' => $license->grace_days,
            ],
            'billing' => $billing ? [
                'status' => $billing->status,
                'current_period_end' => $billing->current_period_end,
                'subscribe_url' => $billing->subscribe_url,
                'day_of_month' => $billing->day_of_month,
                'cycles_paid' => $billing->cycles_paid,
                'max_periods' => $billing->max_periods,
            ] : null,
        ]);
    }

    public function checkout(Request $request, LicenseCheckoutService $service)
    {
        $user = Auth::guard('landlord')->user();

        $license = License::query()
            ->where('account_id', $user->account_id)
            ->orderByDesc('expires_at')
            ->firstOrFail();

        $billing = $service->ensureCheckoutForLicense($license);

        // MVP: devolver URL (front redirige)
        return response()->json([
            'subscribe_url' => $billing->subscribe_url,
        ]);
    }
}
