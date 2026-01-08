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

    public function getPublicKeyPlain(): ?string
    {
        return $this->public_key ? Crypt::decryptString($this->public_key) : null;
    }

    public function getWebhookSecretPlain(): ?string
    {
        return $this->webhook_secret ? Crypt::decryptString($this->webhook_secret) : null;
    }
}
