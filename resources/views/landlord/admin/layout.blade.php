<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin') | JectStore</title>
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
      position: relative;
      z-index: 1;
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

    .nav a:hover,
    .nav a.active {
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

    .page-actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .card {
      background: var(--card);
      border-radius: var(--radius-lg);
      padding: 22px;
      border: 1px solid rgba(216, 226, 243, 0.9);
      box-shadow: var(--shadow);
      animation: rise 0.55s ease both;
    }

    .card + .card {
      margin-top: 16px;
    }

    .card-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 14px;
    }

    .card h3 {
      margin: 0;
      font-size: 18px;
    }

    .muted {
      color: var(--muted);
    }

    .small {
      font-size: 13px;
    }

    .grid {
      display: grid;
      gap: 18px;
    }

    .grid.two {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .stack {
      display: grid;
      gap: 12px;
    }

    .card-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 14px;
      text-decoration: none;
      color: inherit;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card-row:hover {
      transform: translateY(-2px);
      box-shadow: 0 24px 60px rgba(15, 27, 45, 0.18);
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

    .button-secondary:hover {
      border-color: rgba(255, 122, 89, 0.4);
      transform: translateY(-1px);
    }

    .card-actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 16px;
    }

    .form {
      display: grid;
      gap: 14px;
    }

    .field {
      display: grid;
      gap: 6px;
    }

    label {
      font-size: 13px;
      color: var(--muted);
    }

    input,
    select,
    textarea {
      width: 100%;
      padding: 12px 14px;
      border-radius: var(--radius-md);
      border: 1px solid var(--line);
      background: #f7f9fe;
      font-size: 15px;
      transition: border 0.2s ease, box-shadow 0.2s ease;
    }

    textarea {
      min-height: 120px;
      resize: vertical;
    }

    input:focus,
    select:focus,
    textarea:focus {
      outline: none;
      border-color: rgba(255, 122, 89, 0.8);
      box-shadow: 0 0 0 4px rgba(255, 122, 89, 0.15);
      background: #fff;
    }

    .alert {
      padding: 12px 14px;
      border-radius: 12px;
      margin-bottom: 14px;
      font-size: 14px;
    }

    .alert-success {
      background: #f0f9f2;
      border: 1px solid #b7e4c7;
      color: #1b7b4d;
    }

    .meta {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 12px;
      margin-bottom: 18px;
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

    .actions {
      display: grid;
      gap: 8px;
      align-items: center;
      text-align: right;
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

      .grid.two {
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
  <div class="shell">
    <header class="topbar">
      <div class="brand">
        <div class="brand-mark">J</div>
        <div>
          <div class="brand-eyebrow">JectStore Portal</div>
          <div class="brand-title">Landlord Admin</div>
        </div>
      </div>
      <nav class="nav">
        <a href="/admin" class="{{ request()->is('admin') ? 'active' : '' }}">Dashboard</a>
        <a href="/admin/accounts" class="{{ request()->is('admin/accounts*') ? 'active' : '' }}">Accounts</a>
        <a href="/admin/plans" class="{{ request()->is('admin/plans*') ? 'active' : '' }}">Planes</a>
      </nav>
      <form method="POST" action="/logout" class="logout">
        @csrf
        <button type="submit">Salir</button>
      </form>
    </header>

    <main>
      <div class="page-head">
        <div>
          <div class="eyebrow">@yield('eyebrow', 'Superadmin')</div>
          <h1>@yield('title', 'Panel')</h1>
          @hasSection('subtitle')
            <p class="lead">@yield('subtitle')</p>
          @endif
        </div>
        <div class="page-actions">
          @yield('actions')
        </div>
      </div>

      @if(session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
      @endif

      @yield('content')
    </main>

    <footer>
      Desarrollado por <span class="footer-accent">JectStore</span> - Made in Colombia
    </footer>
  </div>
</body>
</html>
