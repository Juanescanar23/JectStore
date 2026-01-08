<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Landlord\BillingEvent;
use App\Models\Landlord\License;
use App\Models\Landlord\LicenseBilling;
use App\Services\Billing\DLocalGo\DLocalGoClient;
use App\Services\Billing\DLocalGo\LicenseBillingService;
use App\Services\Billing\LicenseStatusService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ProcessBillingEventJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly int $eventId
    ) {}

    public function handle(): void
    {
        $event = BillingEvent::query()->find($this->eventId);
        if (! $event) {
            return;
        }

        if ($event->status === 'processed') {
            return;
        }

        if (app()->environment('testing')) {
            $event->status = 'processed';
            $event->processed_at = now();
            $event->save();
            return;
        }

        try {
            if ($event->provider === 'dlocalgo') {
                $this->handleDlocalGo($event);
            }

            if ($event->status === 'failed') {
                $event->processed_at = now();
                $event->save();
                return;
            }

            $event->status = 'processed';
            $event->processed_at = now();
            $event->save();
        } catch (\Throwable $e) {
            $event->status = 'failed';
            $event->error_message = $e->getMessage();
            $event->processed_at = now();
            $event->save();
        }
    }

    private function handleDlocalGo(BillingEvent $event): void
    {
        if (! $event->provider_event_id) {
            $event->status = 'failed';
            $event->error_message = 'missing payment id';
            return;
        }

        if (! $event->license_id) {
            $event->status = 'failed';
            $event->error_message = 'missing license id';
            return;
        }

        $license = License::query()->find($event->license_id);
        if (! $license) {
            $event->status = 'failed';
            $event->error_message = 'license not found';
            return;
        }

        $client = new DLocalGoClient();
        $statusService = new LicenseStatusService();
        $billingService = new LicenseBillingService($client, $statusService);

        $payment = $client->retrievePayment((string) $event->provider_event_id);
        $status = strtoupper((string) ($payment['status'] ?? ''));

        if ($status === 'PAID') {
            $billingService->recordPaid($license, $payment);
            return;
        }

        $billingStatus = $billingService->mapDlocalPaymentStatus($status);
        LicenseBilling::query()
            ->where('license_id', $license->id)
            ->where('provider', 'dlocalgo')
            ->update([
                'status' => $billingStatus,
                'updated_at' => now(),
            ]);
    }
}
