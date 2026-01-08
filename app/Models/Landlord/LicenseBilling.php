<?php

declare(strict_types=1);

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LicenseBilling extends Model
{
    protected $connection = 'landlord';
    protected $table = 'license_billings';

    protected $fillable = [
        'license_id',
        'provider',
        'plan_id',
        'plan_token',
        'subscribe_url',
        'day_of_month',
        'max_periods',
        'cycles_paid',
        'status',
        'current_period_start',
        'current_period_end',
        'grace_days',
        'last_payment_id',
        'last_paid_at',
        'meta',
    ];

    protected $casts = [
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'last_paid_at' => 'datetime',
        'meta' => 'array',
        'cycles_paid' => 'integer',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class, 'license_id');
    }
}
