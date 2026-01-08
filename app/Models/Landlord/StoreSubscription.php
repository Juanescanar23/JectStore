<?php

declare(strict_types=1);

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

final class StoreSubscription extends Model
{
    protected $connection = 'landlord';

    protected $fillable = [
        'account_id',
        'tenant_id',
        'provider',
        'provider_subscription_id',
        'status',
        'current_period_start',
        'current_period_end',
        'amount',
        'currency',
        'grace_days',
        'last_paid_at',
        'meta',
    ];

    protected $casts = [
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'last_paid_at' => 'datetime',
        'meta' => 'array',
    ];
}
