<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $connection = 'landlord';
    protected $table = 'accounts';

    protected $fillable = ['name', 'billing_email', 'status'];

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'account_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(LandlordUser::class, 'account_id');
    }
}
