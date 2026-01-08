<?php

declare(strict_types=1);

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

final class PaymentProviderConfig extends Model
{
    protected $fillable = [
        'provider',
        'environment',
        'access_token',
        'public_key',
        'webhook_secret',
    ];

    public function getAccessTokenPlain(): string
    {
        return Crypt::decryptString($this->access_token);
    }
}
