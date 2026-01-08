<?php

declare(strict_types=1);

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

final class AccountProviderConfig extends Model
{
    protected $connection = 'landlord';
    protected $table = 'account_provider_configs';

    protected $fillable = [
        'account_id',
        'provider',
        'access_token',
        'public_key',
        'webhook_secret',
        'country',
        'currency',
        'grace_days',
    ];

    public function getAccessTokenPlain(): ?string
    {
        return $this->access_token ? Crypt::decryptString($this->access_token) : null;
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
