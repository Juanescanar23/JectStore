<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landlord\Billing;

use App\Models\Landlord\License;
use App\Services\Billing\DLocalGo\LicenseBillingService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class LicensePaymentController extends Controller
{
    public function __invoke(Request $request, License $license, LicenseBillingService $billingService): JsonResponse
    {
        $user = Auth::guard('landlord')->user();

        if ($user && method_exists($user, 'isSuperAdmin') && ! $user->isSuperAdmin()) {
            if (! $user->account_id || $license->account_id !== $user->account_id) {
                abort(403);
            }
        }

        if ($license->expires_at && CarbonImmutable::parse($license->expires_at)->lessThanOrEqualTo(CarbonImmutable::now())) {
            return response()->json([
                'message' => 'license expired',
            ], 410);
        }

        $billing = $billingService->ensureDLocalPlanForLicense($license);

        return response()->json([
            'subscribe_url' => $billing->subscribe_url,
            'plan_token' => $billing->plan_token,
        ]);
    }
}
