<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mercado Pago | JectStore</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg-1: #f7efe6;
      --bg-2: #edf4ff;
      --ink: #0f1b2d;
      --muted: #51607a;
      --card: #ffffff;
      --accent: #ff7a59;
      --accent-dark: #e4623f;
      --line: #d8e2f3;
      --shadow: 0 30px 80px rgba(15, 27, 45, 0.16);
      --radius-lg: 24px;
      --radius-md: 16px;
      --radius-sm: 10px;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      font-family: "Space Grotesk", sans-serif;
      color: var(--ink);
      background:
        radial-gradient(900px 480px at 0% 0%, rgba(255, 122, 89, 0.18), transparent 55%),
        radial-gradient(750px 520px at 100% 15%, rgba(70, 161, 255, 0.16), transparent 55%),
        linear-gradient(135deg, var(--bg-1), var(--bg-2));
    }

    .shell {
      width: min(1200px, 94vw);
      margin: 0 auto;
      padding: 32px 0 56px;
    }

    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 18px;
      padding: 18px 22px;
      border-radius: var(--radius-lg);
      background: rgba(255, 255, 255, 0.86);
      border: 1px solid rgba(216, 226, 243, 0.85);
      box-shadow: 0 18px 40px rgba(15, 27, 45, 0.08);
      backdrop-filter: blur(6px);
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .brand-mark {
      width: 44px;
      height: 44px;
      border-radius: 14px;
      background: linear-gradient(140deg, var(--accent), #ffb07c);
      display: grid;
      place-items: center;
      color: #fff;
      font-weight: 700;
      font-size: 18px;
      box-shadow: 0 12px 30px rgba(255, 122, 89, 0.35);
    }

    .brand-eyebrow {
      font-size: 11px;
      letter-spacing: 0.16em;
      text-transform: uppercase;
      color: var(--muted);
    }

    .brand-title {
      font-size: 16px;
      font-weight: 600;
    }

    .nav {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }

    .nav a {
      text-decoration: none;
      color: var(--muted);
      padding: 8px 14px;
      border-radius: 999px;
      border: 1px solid transparent;
      background: rgba(237, 244, 255, 0.6);
      transition: all 0.2s ease;
      font-size: 14px;
    }

    .nav a.active,
    .nav a:hover {
      color: var(--ink);
      border-color: rgba(255, 122, 89, 0.4);
      background: #fff;
      box-shadow: 0 8px 18px rgba(15, 27, 45, 0.08);
    }

    .logout button {
      border: none;
      padding: 10px 16px;
      border-radius: 999px;
      font-weight: 600;
      font-size: 14px;
      color: #fff;
      background: linear-gradient(135deg, var(--accent), var(--accent-dark));
      box-shadow: 0 12px 24px rgba(255, 122, 89, 0.28);
      cursor: pointer;
    }

    main {
      margin-top: 28px;
      display: grid;
      gap: 22px;
    }

    .page-head {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      gap: 16px;
      animation: rise 0.5s ease both;
    }

    .eyebrow {
      text-transform: uppercase;
      letter-spacing: 0.16em;
      font-size: 11px;
      color: var(--muted);
    }

    h1 {
      margin: 6px 0 6px;
      font-size: clamp(28px, 2.6vw, 38px);
    }

    .lead {
      margin: 0;
      color: var(--muted);
      max-width: 560px;
    }

    .grid {
      display: grid;
      gap: 18px;
    }

    .grid.two {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .card {
      background: var(--card);
      border-radius: var(--radius-lg);
      padding: 22px;
      border: 1px solid rgba(216, 226, 243, 0.9);
      box-shadow: var(--shadow);
      animation: rise 0.55s ease both;
    }

    .card h3 {
      margin: 0 0 8px;
      font-size: 18px;
    }

    .muted {
      color: var(--muted);
    }

    .small {
      font-size: 13px;
    }

    .badge {
      padding: 6px 12px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      background: #f1f5ff;
      color: var(--muted);
    }

    .badge.active {
      background: rgba(35, 167, 94, 0.14);
      color: #1b7b4d;
    }

    .badge.past_due {
      background: rgba(255, 122, 89, 0.18);
      color: #b44a2f;
    }

    .badge.suspended,
    .badge.expired,
    .badge.canceled {
      background: rgba(15, 27, 45, 0.18);
      color: #243248;
    }

    .alert {
      padding: 12px 16px;
      border-radius: 12px;
      border: 1px solid rgba(216, 226, 243, 0.9);
      background: rgba(255, 255, 255, 0.9);
      color: var(--muted);
      font-size: 14px;
    }

    .alert.ok {
      border-color: rgba(35, 167, 94, 0.3);
      color: #1b7b4d;
    }

    .alert.err {
      border-color: rgba(255, 122, 89, 0.3);
      color: #b44a2f;
    }

    label {
      font-size: 13px;
      color: var(--muted);
      display: block;
      margin-bottom: 6px;
    }

    input, select {
      width: 100%;
      padding: 10px 12px;
      border-radius: 12px;
      border: 1px solid rgba(216, 226, 243, 0.9);
      font-family: inherit;
      font-size: 14px;
    }

    .form-row {
      display: grid;
      gap: 12px;
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .form-actions {
      margin-top: 14px;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .button,
    .button-secondary {
      border: none;
      padding: 10px 16px;
      border-radius: 999px;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .button {
      background: linear-gradient(135deg, var(--accent), var(--accent-dark));
      color: #fff;
      box-shadow: 0 12px 24px rgba(255, 122, 89, 0.28);
    }

    .button-secondary {
      background: rgba(237, 244, 255, 0.8);
      color: var(--ink);
      border: 1px solid rgba(216, 226, 243, 0.9);
    }

    .store-card {
      border-radius: var(--radius-md);
      border: 1px solid rgba(216, 226, 243, 0.9);
      padding: 16px;
      display: grid;
      gap: 12px;
      background: #fff;
    }

    .store-head {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
    }

    .store-meta {
      display: grid;
      gap: 8px;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }

    .store-actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      align-items: center;
    }

    footer {
      margin: 42px 0 10px;
      text-align: center;
      color: var(--muted);
      font-size: 12px;
      letter-spacing: 0.14em;
      text-transform: uppercase;
    }

    @keyframes rise {
      from { opacity: 0; transform: translateY(16px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 900px) {
      .grid.two {
        grid-template-columns: 1fr;
      }

      .page-head {
        flex-direction: column;
        align-items: flex-start;
      }

      .form-row {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  @php
    $isBilling = request()->is('portal/billing*');
    $isMp = request()->is('portal/payments/mercadopago*');
    $connected = $config && $config->access_token;
  @endphp
  <div class="shell">
    <div class="topbar">
      <div class="brand">
        <div class="brand-mark">J</div>
        <div>
          <div class="brand-eyebrow">JectStore Portal</div>
          <div class="brand-title">Payments & Suscripciones</div>
        </div>
      </div>
      <nav class="nav">
        <a href="/portal/billing" class="{{ $isBilling ? 'active' : '' }}">Billing</a>
        <a href="/portal/payments/mercadopago" class="{{ $isMp ? 'active' : '' }}">Mercado Pago</a>
      </nav>
      <form class="logout" method="POST" action="/logout">
        @csrf
        <button type="submit">Salir</button>
      </form>
    </div>

    <main>
      <div class="page-head">
        <div>
          <div class="eyebrow">Portal</div>
          <h1>Mercado Pago</h1>
          <p class="lead">Conecta tu cuenta y crea suscripciones mensuales para cada tienda.</p>
        </div>
      </div>

      @if (session('ok'))
        <div class="alert ok">{{ session('ok') }}</div>
      @endif

      @if ($errors->any())
        <div class="alert err">
          {{ $errors->first() }}
        </div>
      @endif

      <div class="grid two">
        <div class="card">
          <div class="store-head">
            <div>
              <h3>Conexion de cuenta</h3>
              <p class="muted small">Guarda las credenciales de Mercado Pago del reseller.</p>
            </div>
            <span class="badge {{ $connected ? 'active' : '' }}">
              {{ $connected ? 'Conectado' : 'No conectado' }}
            </span>
          </div>

          <form method="POST" action="/portal/payments/mercadopago">
            @csrf
            <div class="form-row">
              <div>
                <label>Access Token</label>
                <input name="access_token" type="password" value="{{ old('access_token') }}" required>
              </div>
              <div>
                <label>Webhook Secret</label>
                <input name="webhook_secret" type="password" value="{{ old('webhook_secret') }}" required>
              </div>
            </div>
            <div class="form-row" style="margin-top:12px;">
              <div>
                <label>Public Key (opcional)</label>
                <input name="public_key" type="password" value="{{ old('public_key') }}">
              </div>
              <div>
                <label>Grace Days</label>
                <input name="grace_days" type="number" min="0" max="30" value="{{ old('grace_days', $config->grace_days ?? 5) }}">
              </div>
            </div>
            <div class="form-row" style="margin-top:12px;">
              <div>
                <label>Pais</label>
                <input name="country" value="{{ old('country', $config->country ?? 'AR') }}">
              </div>
              <div>
                <label>Moneda</label>
                <input name="currency" value="{{ old('currency', $config->currency ?? 'ARS') }}">
              </div>
            </div>
            <div class="form-actions">
              <button class="button" type="submit">Guardar conexion</button>
            </div>
          </form>
        </div>

        <div class="card">
          <h3>Resumen de tiendas</h3>
          <p class="muted small">Suscribe tiendas una por una usando el link de checkout de Mercado Pago.</p>
          <div class="store-meta">
            <div>
              <div class="muted small">Account</div>
              <strong>{{ $account->name }}</strong>
            </div>
            <div>
              <div class="muted small">Currency</div>
              <strong>{{ $currency }}</strong>
            </div>
            <div>
              <div class="muted small">Default amount</div>
              <strong>{{ $defaultAmount ? number_format($defaultAmount, 2) : '-' }}</strong>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="store-head">
          <div>
            <h3>Tiendas del reseller</h3>
            <p class="muted small">Crea la suscripcion y comparte el link con la tienda.</p>
          </div>
        </div>

        <div class="grid">
          @forelse($stores as $store)
            @php
              $subscription = $store['subscription'];
              $status = $subscription ? $subscription->status : 'none';
            @endphp
            <div class="store-card">
              <div class="store-head">
                <div>
                  <strong>{{ $store['name'] }}</strong>
                  <div class="muted small">Dominios: {{ $store['domains'] }}</div>
                </div>
                <span class="badge {{ $status }}">{{ $subscription ? strtoupper($subscription->status) : 'SIN SUSCRIPCION' }}</span>
              </div>
              <div class="store-meta">
                <div>
                  <div class="muted small">Periodo fin</div>
                  <strong>{{ $subscription?->current_period_end ?? '-' }}</strong>
                </div>
                <div>
                  <div class="muted small">Monto</div>
                  <strong>{{ $store['default_amount'] ? number_format($store['default_amount'], 2) : '-' }} {{ $store['currency'] }}</strong>
                </div>
                <div>
                  <div class="muted small">Suscripcion</div>
                  <strong>{{ $subscription?->provider_subscription_id ?? '-' }}</strong>
                </div>
              </div>
              <div class="store-actions">
                @if($store['checkout_url'])
                  <a class="button-secondary" href="{{ $store['checkout_url'] }}" target="_blank" rel="noreferrer">Abrir checkout</a>
                @endif
              </div>
              <form method="POST" action="/portal/payments/mercadopago/subscribe/{{ $store['id'] }}">
                @csrf
                <div class="form-row">
                  <div>
                    <label>Email pagador</label>
                    <input name="payer_email" type="email" placeholder="buyer@tienda.com" value="{{ old('payer_email') }}">
                  </div>
                  <div>
                    <label>Monto mensual ({{ $store['currency'] }})</label>
                    <input name="amount" type="number" min="1" step="0.01" value="{{ old('amount', $store['default_amount']) }}">
                  </div>
                </div>
                <div class="form-actions">
                  <button class="button" type="submit" {{ $connected ? '' : 'disabled' }}>
                    {{ $subscription ? 'Recrear suscripcion' : 'Suscribir tienda' }}
                  </button>
                  @if(! $connected)
                    <span class="muted small">Conecta Mercado Pago para habilitar suscripciones.</span>
                  @endif
                </div>
              </form>
            </div>
          @empty
            <div class="muted">No hay tiendas registradas.</div>
          @endforelse
        </div>
      </div>
    </main>

    <footer>DESARROLLADO POR JECTSTORE - MADE IN COLOMBIA</footer>
  </div>
</body>
</html>
