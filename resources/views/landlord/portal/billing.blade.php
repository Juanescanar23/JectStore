<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Portal Billing | JectStore</title>
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
      padding: 32px 0 48px;
    }

    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 18px;
      padding: 18px 22px;
      border-radius: var(--radius-lg);
      background: rgba(255, 255, 255, 0.82);
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
      gap: 20px;
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
      max-width: 520px;
    }

    .grid {
      display: grid;
      gap: 18px;
    }

    .grid.two {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .grid.three {
      grid-template-columns: repeat(3, minmax(0, 1fr));
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

    .badge-success {
      background: rgba(35, 167, 94, 0.14);
      color: #1b7b4d;
    }

    .badge-warning {
      background: rgba(255, 122, 89, 0.2);
      color: #b44a2f;
    }

    .button,
    .button-secondary {
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 10px 16px;
      border-radius: 999px;
      font-weight: 600;
      font-size: 14px;
      border: 1px solid transparent;
      transition: all 0.2s ease;
      cursor: pointer;
    }

    .button {
      color: #fff;
      background: linear-gradient(135deg, var(--accent), var(--accent-dark));
      box-shadow: 0 12px 24px rgba(255, 122, 89, 0.28);
    }

    .button-secondary {
      color: var(--ink);
      background: #fff;
      border-color: rgba(216, 226, 243, 0.9);
      box-shadow: 0 8px 18px rgba(15, 27, 45, 0.08);
    }

    .meta {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 12px;
      margin-bottom: 12px;
    }

    .meta span {
      display: block;
      font-size: 12px;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.08em;
    }

    .meta strong {
      font-size: 16px;
      font-weight: 600;
    }

    .progress {
      background: #f2f5ff;
      border-radius: 999px;
      height: 10px;
      overflow: hidden;
      margin-top: 8px;
    }

    .progress > span {
      display: block;
      height: 100%;
      background: linear-gradient(135deg, var(--accent), var(--accent-dark));
    }

    .list {
      display: grid;
      gap: 10px;
    }

    .list-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 12px;
      padding: 12px 14px;
      border-radius: var(--radius-md);
      background: #f7f9fe;
      border: 1px solid rgba(216, 226, 243, 0.8);
    }

    .list-row p {
      margin: 4px 0 0;
      color: var(--muted);
      font-size: 13px;
    }

    footer {
      margin-top: 28px;
      text-align: center;
      font-size: 12px;
      color: var(--muted);
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .footer-accent {
      color: var(--accent);
      font-weight: 600;
    }

    @media (max-width: 980px) {
      .topbar {
        flex-direction: column;
        align-items: stretch;
      }

      .page-head {
        flex-direction: column;
        align-items: flex-start;
      }

      .grid.two,
      .grid.three {
        grid-template-columns: 1fr;
      }
    }

    @keyframes rise {
      from {
        opacity: 0;
        transform: translateY(14px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .page-head,
      .card {
        animation: none;
      }
    }
  </style>
</head>
<body>
  @php
    $account = $summary['account'];
    $license = $summary['license'];
    $billing = $summary['billing'];
    $usage = $summary['usage'];
    $domains = $summary['domains'];
    $owners = $summary['owners'];
    $events = $summary['events'];
    $state = $summary['access_state'];
    $periodStart = $summary['cycle']['period_start'];
    $periodEnd = $summary['cycle']['period_end'];
    $graceEndsAt = $summary['cycle']['grace_ends_at'];
    $tenantsCount = (int) ($usage['tenants_count'] ?? 0);
    $maxTenants = (int) ($usage['max_tenants'] ?? 0);
    $usagePercent = $maxTenants > 0 ? min(100, (int) round(($tenantsCount / $maxTenants) * 100)) : 0;
    $amountLabel = $license ? number_format((float) $license['price_usd'], 2) . ' ' . strtoupper((string) $license['currency']) : '-';
  @endphp

  <div class="shell">
    <header class="topbar">
      <div class="brand">
        <div class="brand-mark">J</div>
        <div>
          <div class="brand-eyebrow">JectStore Portal</div>
          <div class="brand-title">Portal Comprador</div>
        </div>
      </div>
      <nav class="nav">
        <a class="active" href="/portal/billing">Billing</a>
      </nav>
      <form method="POST" action="/logout" class="logout">
        @csrf
        <button type="submit">Salir</button>
      </form>
    </header>

    <main>
      <div class="page-head">
        <div>
          <div class="eyebrow">Billing</div>
          <h1>Estado de licencia</h1>
          <p class="lead">Resumen de tu plan, ciclo mensual, consumo de tiendas y pagos recientes.</p>
        </div>
        <div class="page-actions">
          @if($billing && $billing['subscribe_url'])
            <a class="button" href="{{ $billing['subscribe_url'] }}" target="_blank" rel="noopener">Pagar ahora</a>
          @else
            <form class="checkout-form" method="POST" action="/portal/billing/dlocal/checkout">
              @csrf
              <button type="submit" class="button">Generar link de pago</button>
            </form>
          @endif
        </div>
      </div>

      @if(! $license)
        <div class="card">
          <h3>Sin licencia activa</h3>
          <p class="muted">Contacta al equipo para activar tu primera licencia.</p>
        </div>
      @else
        <div class="grid two">
          <section class="card">
            <div class="meta">
              <div>
                <span>Plan</span>
                <strong>{{ $license['plan_name'] ?? $license['plan_code'] }}</strong>
              </div>
              <div>
                <span>Estado</span>
                <strong>{{ $state }}</strong>
              </div>
              <div>
                <span>Valor mensual</span>
                <strong>{{ $amountLabel }}</strong>
              </div>
            </div>
            <p class="muted small">Inicio: {{ $license['starts_at']?->format('Y-m-d') }} · Vence: {{ $license['expires_at']?->format('Y-m-d') }}</p>
            <p class="muted small">Gracia: {{ $license['grace_days'] ?? '-' }} dias</p>
          </section>

          <section class="card">
            <h3>Ciclo mensual</h3>
            <div class="meta">
              <div>
                <span>Periodo actual</span>
                <strong>{{ $periodStart?->format('Y-m-d') ?? '-' }} -> {{ $periodEnd?->format('Y-m-d') ?? '-' }}</strong>
              </div>
              <div>
                <span>Gracia hasta</span>
                <strong>{{ $graceEndsAt?->format('Y-m-d') ?? '-' }}</strong>
              </div>
              <div>
                <span>Dia ancla</span>
                <strong>{{ $billing['day_of_month'] ?? '-' }}</strong>
              </div>
            </div>
            <p class="muted small">Ciclos pagados: {{ $billing['cycles_paid'] ?? 0 }} / {{ $billing['max_periods'] ?? '-' }}</p>
          </section>
        </div>

        <div class="grid three">
          <section class="card">
            <h3>Consumo de tiendas</h3>
            <p class="muted">{{ $tenantsCount }} / {{ $maxTenants }}</p>
            <div class="progress"><span style="width: {{ $usagePercent }}%"></span></div>
            <p class="muted small">{{ $usagePercent }}% de la capacidad actual.</p>
          </section>

          <section class="card">
            <h3>Dominios verificados</h3>
            <p class="muted">{{ $domains['count'] }} dominios activos</p>
            <div class="list">
              @forelse($domains['items'] as $domain)
                <div class="list-row">
                  <div>
                    <strong>{{ $domain['root_domain'] }}</strong>
                    <p>{{ $domain['portal_host'] }}</p>
                  </div>
                  <span class="badge">{{ $domain['status'] }}</span>
                </div>
              @empty
                <div class="list-row">
                  <div>
                    <strong>Sin dominios</strong>
                    <p>Agrega un dominio para activar el portal.</p>
                  </div>
                </div>
              @endforelse
            </div>
          </section>

          <section class="card">
            <h3>Owners</h3>
            <div class="list">
              @forelse($owners as $owner)
                <div class="list-row">
                  <div>
                    <strong>{{ $owner->name }}</strong>
                    <p>{{ $owner->email }} · {{ $owner->role }}</p>
                  </div>
                  <span class="badge {{ $owner->status === 'active' ? 'badge-success' : 'badge-warning' }}">{{ $owner->status }}</span>
                </div>
              @empty
                <div class="list-row">
                  <div>
                    <strong>Sin owners</strong>
                    <p>Agrega un usuario account_owner para este account.</p>
                  </div>
                </div>
              @endforelse
            </div>
          </section>
        </div>

        <section class="card">
          <h3>Ultimos eventos dLocal</h3>
          <div class="list">
            @forelse($events as $event)
              <div class="list-row">
                <div>
                  <strong>{{ $event->event_type ?? 'event' }}</strong>
                  <p>ID: {{ $event->provider_event_id }} · {{ $event->created_at?->format('Y-m-d H:i') }}</p>
                </div>
                <span class="badge">{{ $event->status }}</span>
              </div>
            @empty
              <div class="list-row">
                <div>
                  <strong>Sin eventos</strong>
                  <p>Aun no hay eventos de pago registrados.</p>
                </div>
              </div>
            @endforelse
          </div>
        </section>
      @endif
    </main>

    <footer>
      Desarrollado por <span class="footer-accent">JectStore</span> - Made in Colombia
    </footer>
  </div>

  <script>
    document.querySelectorAll('.checkout-form').forEach(function (form) {
      form.addEventListener('submit', async function (event) {
        event.preventDefault();
        const token = form.querySelector('input[name="_token"]').value;
        const response = await fetch(form.action, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token,
          },
        });
        if (!response.ok) {
          alert('No se pudo generar el link de pago.');
          return;
        }
        const data = await response.json();
        if (data.subscribe_url) {
          window.location.href = data.subscribe_url;
        } else {
          alert('No se recibio link de pago.');
        }
      });
    });
  </script>
</body>
</html>
