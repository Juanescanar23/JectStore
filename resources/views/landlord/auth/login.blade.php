<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>JectStore | Acceso Portal</title>
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
      --shadow: 0 30px 80px rgba(15, 27, 45, 0.18);
      --radius-lg: 24px;
      --radius-md: 14px;
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
        radial-gradient(900px 480px at 0% 0%, rgba(255, 122, 89, 0.22), transparent 55%),
        radial-gradient(750px 520px at 100% 15%, rgba(70, 161, 255, 0.18), transparent 55%),
        linear-gradient(135deg, var(--bg-1), var(--bg-2));
      display: flex;
      align-items: center;
      justify-content: center;
    }

    body::after {
      content: "";
      position: fixed;
      inset: auto -15% 5% auto;
      width: 320px;
      height: 320px;
      background: radial-gradient(circle, rgba(255, 204, 153, 0.5), transparent 70%);
      filter: blur(6px);
      z-index: 0;
      pointer-events: none;
    }

    main {
      width: min(1040px, 94vw);
      display: grid;
      grid-template-columns: 1.1fr 0.9fr;
      gap: 48px;
      align-items: center;
      padding: 48px 24px;
      position: relative;
      z-index: 1;
    }

    .hero {
      display: grid;
      gap: 18px;
      animation: floatIn 0.6s ease forwards;
      opacity: 0;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 14px;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      font-size: 12px;
      color: var(--muted);
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

    .hero h1 {
      font-size: clamp(30px, 3vw, 40px);
      margin: 0;
      line-height: 1.1;
    }

    .hero p {
      margin: 0;
      color: var(--muted);
      font-size: 16px;
      max-width: 420px;
    }

    .hero ul {
      list-style: none;
      padding: 0;
      margin: 12px 0 0;
      display: grid;
      gap: 10px;
      color: var(--muted);
      font-size: 14px;
    }

    .hero li::before {
      content: "";
      display: inline-block;
      width: 10px;
      height: 10px;
      border-radius: 3px;
      background: var(--accent);
      margin-right: 10px;
      transform: translateY(1px);
    }

    .card {
      background: var(--card);
      border-radius: var(--radius-lg);
      padding: 32px;
      border: 1px solid rgba(216, 226, 243, 0.8);
      box-shadow: var(--shadow);
      animation: floatIn 0.7s ease forwards;
      animation-delay: 0.08s;
      opacity: 0;
    }

    .card-header {
      margin-bottom: 18px;
    }

    .card-header h2 {
      margin: 0;
      font-size: 22px;
    }

    .card-header span {
      color: var(--muted);
      font-size: 13px;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .error {
      background: #fff1f0;
      color: #9b2c1f;
      border: 1px solid #ffc5bf;
      padding: 12px 14px;
      border-radius: 12px;
      margin-bottom: 16px;
      font-size: 14px;
    }

    label {
      font-size: 13px;
      color: var(--muted);
      display: inline-block;
      margin-bottom: 6px;
    }

    input {
      width: 100%;
      padding: 12px 14px;
      border-radius: var(--radius-md);
      border: 1px solid var(--line);
      background: #f7f9fe;
      font-size: 15px;
      transition: border 0.2s ease, box-shadow 0.2s ease;
    }

    input:focus {
      outline: none;
      border-color: rgba(255, 122, 89, 0.8);
      box-shadow: 0 0 0 4px rgba(255, 122, 89, 0.15);
      background: #fff;
    }

    .field {
      margin-bottom: 14px;
      animation: fadeUp 0.5s ease forwards;
      opacity: 0;
    }

    .field:nth-of-type(1) { animation-delay: 0.12s; }
    .field:nth-of-type(2) { animation-delay: 0.18s; }

    .actions {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-top: 10px;
      animation: fadeUp 0.5s ease forwards;
      animation-delay: 0.24s;
      opacity: 0;
    }

    button {
      border: none;
      padding: 12px 20px;
      border-radius: 999px;
      font-weight: 600;
      font-size: 15px;
      color: #fff;
      background: linear-gradient(135deg, var(--accent), var(--accent-dark));
      box-shadow: 0 12px 24px rgba(255, 122, 89, 0.3);
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    button:hover {
      transform: translateY(-1px);
      box-shadow: 0 16px 26px rgba(255, 122, 89, 0.32);
    }

    .hint {
      font-size: 12px;
      color: var(--muted);
    }

    footer {
      grid-column: 1 / -1;
      display: flex;
      justify-content: center;
      margin-top: 12px;
      font-size: 12px;
      color: var(--muted);
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .footer-accent {
      color: var(--accent);
      font-weight: 600;
    }

    @media (max-width: 860px) {
      main {
        grid-template-columns: 1fr;
      }

      .hero {
        text-align: center;
        justify-items: center;
      }

      .hero p {
        max-width: 520px;
      }

      .card {
        width: min(520px, 100%);
        margin: 0 auto;
      }

      .actions {
        flex-direction: column;
        align-items: stretch;
      }
    }

    @keyframes floatIn {
      from {
        opacity: 0;
        transform: translateY(16px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeUp {
      from {
        opacity: 0;
        transform: translateY(12px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .hero,
      .card,
      .field,
      .actions {
        animation: none;
        opacity: 1;
      }
    }
  </style>
</head>
<body>
  <main>
    <section class="hero">
      <div class="brand">
        <div class="brand-mark">J</div>
        <div>JectStore Portal</div>
      </div>
      <h1>Acceso al panel landlord</h1>
      <p>Administra cuentas, licencias y tiendas sin salir del entorno central.</p>
      <ul>
        <li>Control de licencias y vencimientos</li>
        <li>Gestor de tiendas y usuarios</li>
        <li>Billing y estado en tiempo real</li>
      </ul>
    </section>

    <section class="card">
      <div class="card-header">
        <span>Login</span>
        <h2>Acceso Portal</h2>
      </div>

      @if ($errors->any())
        <div class="error" role="alert">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="/login">
        @csrf
        <div class="field">
          <label for="email">Email</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
        </div>

        <div class="field">
          <label for="password">Password</label>
          <input id="password" type="password" name="password" autocomplete="current-password" required>
        </div>

        <div class="actions">
          <div class="hint">Dominio: app.jectstore.test</div>
          <button type="submit">Entrar</button>
        </div>
      </form>
    </section>
    <footer>
      Desarrollado por <span class="footer-accent">&nbsp;JectStore</span>&nbsp;· Made in Colombia
    </footer>
  </main>
</body>
</html>
