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

    h2 {
      margin: 0 0 10px;
      font-size: 20px;
    }

    .lead {
      margin: 0;
      color: var(--muted);
      max-width: 560px;
    }

    .status-chip {
      padding: 8px 16px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      background: #f1f5ff;
      color: var(--muted);
    }

    .status-chip.active {
      background: rgba(35, 167, 94, 0.14);
      color: #1b7b4d;
    }

    .status-chip.grace {
      background: rgba(255, 122, 89, 0.18);
      color: #b44a2f;
    }

    .status-chip.suspended,
    .status-chip.expired,
    .status-chip.cancelled,
    .status-chip.canceled {
      background: rgba(15, 27, 45, 0.18);
      color: #243248;
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

    .button,
    .button-secondary {
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 10px 18px;
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

    .action {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      background: linear-gradient(135deg, rgba(255, 122, 89, 0.08), rgba(70, 161, 255, 0.08));
    }

    .meta {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 12px;
      margin-bottom: 6px;
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

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
    }

    th,
    td {
      padding: 12px 10px;
      text-align: left;
      border-bottom: 1px solid rgba(216, 226, 243, 0.7);
    }

    th {
      text-transform: uppercase;
      letter-spacing: 0.08em;
      font-size: 11px;
      color: var(--muted);
    }

    .section-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      margin-bottom: 12px;
    }

    .owner-action {
      display: flex;
      flex-direction: column;
      gap: 6px;
      align-items: flex-end;
    }

    .owner-action .small {
      margin: 0;
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

      .action {
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
    $account = $summary['account'] ?? null;
    $license = $summary['license'] ?? [];
    $hasLicense = ! empty($summary['license']);
    $billing = $summary['billing'] ?? [];
    $global = $summary['global'] ?? [];
    $cycle = $summary['cycle'] ?? [];
    $contract = $summary['contract'] ?? [];
    $usage = $summary['usage'] ?? [];
    $domains = $summary['domains'] ?? [];
    $owners = $summary['owners'] ?? [];
    $ownersCount = (int) ($summary['owners_count'] ?? 0);
    $ownersLimit = (int) ($summary['owners_limit'] ?? 3);
    $events = $summary['events'] ?? [];

    $state = strtolower((string) ($summary['access_state'] ?? 'inactive'));
    $statusLabel = (string) ($global['status'] ?? strtoupper($state));
    $statusMessage = (string) ($global['message'] ?? '');

    $formatDate = function ($value, bool $withTime = false): string {
        if (! $value) {
            return '-';
        }
        if ($value instanceof \Carbon\CarbonInterface) {
            return $value->format($withTime ? 'Y-m-d H:i' : 'Y-m-d');
        }
        try {
            return \Carbon\CarbonImmutable::parse($value)->format($withTime ? 'Y-m-d H:i' : 'Y-m-d');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    };

    $planName = $license['plan_name'] ?? $license['plan_code'] ?? '-';
    $maxTenants = (int) ($license['max_tenants'] ?? 0);
    $amountValue = $hasLicense && isset($license['amount']) ? number_format((float) $license['amount'], 2) : null;
    $currency = strtoupper((string) ($license['currency'] ?? ''));
    $amountLabel = $amountValue ? $amountValue . ' ' . $currency : '-';

    $tenantsCount = (int) ($usage['tenants_count'] ?? 0);
    $usagePercent = $maxTenants > 0 ? min(100, (int) round(($tenantsCount / $maxTenants) * 100)) : 0;

    $periodEnd = $cycle['period_end'] ?? null;
    $graceEndsAt = $cycle['grace_ends_at'] ?? null;
    $daysRemaining = $cycle['days_remaining'] ?? null;
    $nextDueDate = $cycle['next_due_date'] ?? null;
    $subscribeUrl = $billing['subscribe_url'] ?? null;
    $paymentStatus = $billing['status'] ?? '-';
    $paymentStatusLabel = $paymentStatus;
    if ($state === 'grace' && $paymentStatus === 'past_due') {
        $paymentStatusLabel = 'grace (past_due)';
    }
  @endphp

  <div class="shell">
    <header class="topbar">
      <div class="brand">
        <div class="brand-mark">J</div>
        <div>
          <div class="brand-eyebrow">JectStore Portal</div>
          <div class="brand-title">Portal comprador</div>
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
          <div class="eyebrow">Resumen general</div>
          <h1>{{ $account['name'] ?? 'Cuenta' }}</h1>
          <p class="lead">{{ $statusMessage }}</p>
        </div>
        <div>
          <span class="status-chip {{ $state }}">{{ $statusLabel }}</span>
          <div class="small muted">Estado global</div>
        </div>
      </div>

      @if(! $hasLicense)
        <section class="card">
          <h2>Sin licencia activa</h2>
          <p class="muted">Contacta al equipo para activar tu licencia y comenzar a operar.</p>
        </section>
      @else
        <section class="card action">
          <div>
            <h2>Acciones de pago</h2>
            @if(in_array($state, ['grace', 'suspended'], true))
              <p class="muted">Fecha de cobro: {{ $formatDate($periodEnd) }}</p>
              <p class="muted">Limite con gracia: {{ $formatDate($graceEndsAt) }}</p>
              <p class="muted">Dias restantes: {{ $daysRemaining !== null ? $daysRemaining : '-' }}</p>
            @elseif($state === 'active')
              <p class="muted">Proximo cobro: {{ $formatDate($nextDueDate) }}</p>
            @elseif(in_array($state, ['expired', 'cancelled', 'canceled'], true))
              <p class="muted">Contrato finalizo: {{ $formatDate($license['expires_at'] ?? null) }}</p>
              <p class="muted small">Solicitar renovacion con el equipo comercial.</p>
            @else
              <p class="muted">Validando estado de la licencia.</p>
            @endif
          </div>
          <div>
            @if(in_array($state, ['grace', 'suspended'], true))
              @if($subscribeUrl)
                <a class="button" href="{{ $subscribeUrl }}" target="_blank" rel="noopener">Pagar ahora</a>
              @else
                <form class="checkout-form" method="POST" action="/portal/billing/dlocal/checkout">
                  @csrf
                  <button type="submit" class="button">Generar link de pago</button>
                </form>
              @endif
            @endif
          </div>
        </section>

        <div class="grid three">
          <section class="card">
            <h3>Plan</h3>
            <div class="meta">
              <div>
                <span>Nombre</span>
                <strong>{{ $planName }}</strong>
              </div>
              <div>
                <span>Max tiendas</span>
                <strong>{{ $maxTenants ?: '-' }}</strong>
              </div>
              <div>
                <span>Mensual</span>
                <strong>{{ $amountLabel }}</strong>
              </div>
            </div>
          </section>

          <section class="card">
            <h3>Contrato</h3>
            <div class="meta">
              <div>
                <span>Inicio</span>
                <strong>{{ $formatDate($license['starts_at'] ?? null) }}</strong>
              </div>
              <div>
                <span>Vence</span>
                <strong>{{ $formatDate($license['expires_at'] ?? null) }}</strong>
              </div>
              <div>
                <span>Meses restantes</span>
                <strong>{{ $contract['months_remaining'] ?? '-' }}</strong>
              </div>
            </div>
          </section>

          <section class="card">
            <h3>Ciclo</h3>
            <div class="meta">
              <div>
                <span>Dia ancla</span>
                <strong>{{ $billing['day_of_month'] ?? '-' }}</strong>
              </div>
              <div>
                <span>Periodo fin</span>
                <strong>{{ $formatDate($billing['current_period_end'] ?? null) }}</strong>
              </div>
              <div>
                <span>Gracia</span>
                <strong>{{ $billing['grace_days'] ?? $license['grace_days'] ?? '-' }} dias</strong>
              </div>
            </div>
          </section>

          <section class="card">
            <h3>Pago</h3>
            <div class="meta">
              <div>
                <span>Status</span>
                <strong>{{ $paymentStatusLabel }}</strong>
              </div>
              <div>
                <span>Ultimo pago</span>
                <strong>{{ $formatDate($billing['last_paid_at'] ?? null, true) }}</strong>
              </div>
              <div>
                <span>Ultimo ID</span>
                <strong>{{ $billing['last_payment_id'] ?? '-' }}</strong>
              </div>
            </div>
          </section>

          <section class="card">
            <h3>Tiendas</h3>
            <div class="meta">
              <div>
                <span>Consumo</span>
                <strong>{{ $tenantsCount }} / {{ $maxTenants ?: '-' }}</strong>
              </div>
            </div>
            <div class="progress"><span style="width: {{ $usagePercent }}%"></span></div>
            <p class="muted small">{{ $usagePercent }}% de la capacidad actual.</p>
          </section>

          <section class="card">
            <h3>Dominios</h3>
            <div class="meta">
              <div>
                <span>Verificados</span>
                <strong>{{ $domains['count'] ?? 0 }}</strong>
              </div>
            </div>
            <p class="muted small">Dominios activos en el portal.</p>
          </section>
        </div>

        <section class="card">
          <div class="section-head">
            <div>
              <h3>Checkout dLocal</h3>
              <p class="muted small">Genera tu link de pago o abre el checkout actual.</p>
            </div>
            <div>
              @if($subscribeUrl)
                <a class="button" href="{{ $subscribeUrl }}" target="_blank" rel="noopener">Pagar ahora</a>
              @else
                <form class="checkout-form" method="POST" action="/portal/billing/dlocal/checkout">
                  @csrf
                  <button type="submit" class="button">Generar link de pago</button>
                </form>
              @endif
            </div>
          </div>
        </section>

        <section class="card">
          <div class="section-head">
            <div>
              <h3>Actividad de pagos</h3>
              <p class="muted small">Ultimos 10 eventos de dLocal.</p>
            </div>
          </div>
          <div style="overflow-x:auto;">
            <table>
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Evento</th>
                  <th>Status</th>
                  <th>Procesado</th>
                  <th>Error</th>
                </tr>
              </thead>
              <tbody>
                @forelse($events as $event)
                  <tr>
                    <td>{{ $formatDate($event->created_at ?? null, true) }}</td>
                    <td>{{ $event->provider_event_id ?? '-' }}</td>
                    <td>{{ $event->status ?? '-' }}</td>
                    <td>{{ $formatDate($event->processed_at ?? null, true) }}</td>
                    <td>{{ $event->error_message ?? '-' }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="muted">Sin eventos registrados.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </section>

        <section class="card">
          <div class="section-head">
            <div>
              <h3>Equipo Owners</h3>
              <p class="muted small">{{ $ownersCount }} / {{ $ownersLimit }} owners activos.</p>
            </div>
            <div class="owner-action">
              <button class="button-secondary" {{ $ownersCount >= $ownersLimit ? 'disabled' : '' }}>Invitar owner</button>
              @if($ownersCount >= $ownersLimit)
                <p class="small muted">Limite alcanzado</p>
              @endif
            </div>
          </div>
          <div class="list">
            @forelse($owners as $owner)
              <div class="list-row">
                <div>
                  <strong>{{ $owner->name }}</strong>
                  <p>{{ $owner->email }}</p>
                </div>
                <span class="badge">{{ $owner->status }}</span>
              </div>
            @empty
              <div class="list-row">
                <div>
                  <strong>Sin owners</strong>
                  <p>Agrega usuarios account_owner al portal.</p>
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
