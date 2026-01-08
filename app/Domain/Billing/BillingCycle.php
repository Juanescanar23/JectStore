<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use Carbon\CarbonImmutable;

final class BillingCycle
{
    /**
     * Calcula la proxima fecha de cobro basada en un dia ancla (1..31).
     * Regla: mes siguiente, mismo anchorDay si existe, si no ultimo dia del mes.
     */
    public static function nextDueDate(CarbonImmutable $from, int $anchorDay): CarbonImmutable
    {
        $anchorDay = max(1, min(31, $anchorDay));

        $nextMonth = $from->addMonth();
        $lastDay = (int) $nextMonth->endOfMonth()->day;
        $day = min($anchorDay, $lastDay);

        // normalizamos hora a 00:00 para comparaciones operativas
        return $nextMonth->setDay($day)->startOfDay();
    }

    public static function graceEndsAt(CarbonImmutable $dueDate, int $graceDays): CarbonImmutable
    {
        $graceDays = max(0, $graceDays);
        return $dueDate->addDays($graceDays)->endOfDay();
    }
}
