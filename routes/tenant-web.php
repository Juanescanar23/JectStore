<?php

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Controllers\TenantAssetsController;

Route::get('/storage/{path?}', [TenantAssetsController::class, 'asset'])
    ->where('path', '.*');
