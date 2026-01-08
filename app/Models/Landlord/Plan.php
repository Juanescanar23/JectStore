<?php

declare(strict_types=1);

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

final class Plan extends Model
{
    protected $connection = 'landlord';
    protected $table = 'plans';

    protected $fillable = [
        'code',
        'name',
        'price_usd',
        'contract_months',
        'grace_days',
        'max_tenants',
        'features',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'price_usd' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
