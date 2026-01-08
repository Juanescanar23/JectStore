<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class License extends Model
{
    protected $connection = 'landlord';
    protected $table = 'licenses';

    protected $fillable = [
        'account_id',
        'plan_code',
        'plan_name',
        'max_tenants',
        'starts_at',
        'expires_at',
        'status',
        'grace_days',
        'notes',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
