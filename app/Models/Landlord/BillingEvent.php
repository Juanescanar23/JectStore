<?php

declare(strict_types=1);

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class BillingEvent extends Model
{
    protected $connection = 'landlord';
    protected $table = 'billing_events';

    protected $fillable = [
        'provider',
        'event_type',
        'provider_event_id',
        'account_id',
        'tenant_id',
        'license_id',
        'payload_raw',
        'payload_hash',
        'signature_header',
        'status',
        'processed_at',
        'error_message',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class, 'license_id');
    }
}
