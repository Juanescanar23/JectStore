<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landlord\AuthController;
use App\Http\Controllers\Landlord\Admin\DashboardController;
use App\Http\Controllers\Landlord\Admin\AccountsController;
use App\Http\Controllers\Landlord\Admin\LicensesController;
use App\Http\Controllers\Landlord\Admin\AccountUsersController;
use App\Http\Controllers\Landlord\Admin\PlansController;
use App\Http\Controllers\Landlord\Billing\LicensePaymentController;
use App\Http\Controllers\Landlord\Portal\BillingController;
use App\Http\Controllers\Landlord\Portal\MercadoPagoController;
use App\Http\Controllers\Landlord\Webhooks\DLocalGoWebhookController;
use App\Http\Controllers\Landlord\Webhooks\MercadoPagoWebhookController;
use App\Http\Controllers\Landlord\Webhooks\MercadoPagoStoreWebhookController;

foreach ((array) config('tenancy.central_domains', []) as $domain) {
    Route::domain($domain)->get('/', function () {
        return redirect('/admin');
    });
}

Route::get('/__landlord_ping', fn() => response('landlord ok', 200));

Route::post('/webhooks/dlocalgo', DLocalGoWebhookController::class)->name('webhooks.dlocalgo');
Route::post('/webhooks/mercadopago/{tenantId}', MercadoPagoStoreWebhookController::class)->name('webhooks.mercadopago.store');
Route::post('/webhooks/mercadopago', MercadoPagoWebhookController::class)->name('webhooks.mercadopago');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:landlord');

Route::middleware(['auth:landlord', 'portal.license'])
    ->prefix('billing')
    ->group(function () {
        Route::post('/licenses/{license}/pay', LicensePaymentController::class)->name('billing.licenses.pay');
    });

Route::middleware(['auth:landlord', 'portal.license'])
    ->prefix('portal')
    ->group(function () {
        Route::get('/billing', [BillingController::class, 'index'])->name('portal.billing');
        Route::get('/billing/status', [BillingController::class, 'status'])->name('portal.billing.status');
        Route::post('/billing/dlocal/checkout', [BillingController::class, 'checkout']);

        Route::get('/payments/mercadopago', [MercadoPagoController::class, 'index'])->name('portal.mercadopago');
        Route::post('/payments/mercadopago', [MercadoPagoController::class, 'store']);
        Route::post('/payments/mercadopago/subscribe/{tenantId}', [MercadoPagoController::class, 'subscribe']);
    });

Route::middleware(['auth:landlord', 'role:superadmin', 'portal.license'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index']);

        Route::get('/accounts', [AccountsController::class, 'index']);
        Route::get('/accounts/create', [AccountsController::class, 'create']);
        Route::post('/accounts', [AccountsController::class, 'store']);
        Route::get('/accounts/{account}', [AccountsController::class, 'show']);

        Route::get('/accounts/{account}/licenses/create', [LicensesController::class, 'create']);
        Route::post('/accounts/{account}/licenses', [LicensesController::class, 'store']);
        Route::get('/accounts/{account}/licenses/{license}/edit', [LicensesController::class, 'edit']);
        Route::put('/accounts/{account}/licenses/{license}', [LicensesController::class, 'update']);

        Route::get('/accounts/{account}/users/create', [AccountUsersController::class, 'create']);
        Route::post('/accounts/{account}/users', [AccountUsersController::class, 'store']);

        Route::get('/plans', [PlansController::class, 'index']);
        Route::get('/plans/create', [PlansController::class, 'create']);
        Route::post('/plans', [PlansController::class, 'store']);
        Route::get('/plans/{plan}/edit', [PlansController::class, 'edit']);
        Route::put('/plans/{plan}', [PlansController::class, 'update']);
    });
