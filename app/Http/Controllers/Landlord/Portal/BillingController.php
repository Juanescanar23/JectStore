<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landlord\Portal;

use App\Http\Controllers\Controller;
use App\Models\Landlord\License;
use App\Services\Billing\DLocalGo\LicenseCheckoutService;
use App\Services\Portal\PortalBillingSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class BillingController extends Controller
{
    public function status(PortalBillingSummaryService $summaryService)
    {
        $user = Auth::guard('landlord')->user();
        $summary = $summaryService->buildForUser($user)->toArray();

        return response()->json($summary);
    }

    public function index(PortalBillingSummaryService $summaryService)
    {
        $user = Auth::guard('landlord')->user();
        $summary = $summaryService->buildForUser($user)->toArray();

        if (request()->expectsJson()) {
            return response()->json($summary);
        }

        return view('portal.billing', compact('summary'));
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
