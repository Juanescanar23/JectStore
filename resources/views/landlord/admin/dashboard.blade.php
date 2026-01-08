@extends('landlord.admin.layout')

@section('title', 'Superadmin')
@section('subtitle', 'Panel central para administrar cuentas, licencias y usuarios.')

@section('content')
  <div class="grid two">
    <div class="card">
      <div class="card-head">
        <h3>Accounts</h3>
        <span class="badge">Central</span>
      </div>
      <p class="muted">Crea clientes, gestiona licencias y usuarios desde un solo lugar.</p>
      <div class="card-actions">
        <a class="button" href="/admin/accounts">Ver accounts</a>
        <a class="button-secondary" href="/admin/accounts/create">Crear account</a>
      </div>
    </div>

    <div class="card">
      <div class="card-head">
        <h3>Planes</h3>
        <span class="badge">Comercial</span>
      </div>
      <p class="muted">Define precios, limites y condiciones para nuevas licencias.</p>
      <div class="card-actions">
        <a class="button" href="/admin/plans">Ver planes</a>
        <a class="button-secondary" href="/admin/plans/create">Crear plan</a>
      </div>
    </div>

    <div class="card">
      <div class="card-head">
        <h3>Licencias</h3>
        <span class="badge">Billing</span>
      </div>
      <p class="muted">Controla vencimientos, estados y renovaciones en tiempo real.</p>
      <div class="card-actions">
        <a class="button-secondary" href="/portal/billing">Ir a Billing</a>
      </div>
    </div>
  </div>
@endsection
