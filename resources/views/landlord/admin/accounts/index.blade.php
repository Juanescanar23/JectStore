@extends('landlord.admin.layout')

@section('title', 'Accounts')
@section('subtitle', 'Clientes activos y cuentas registradas en el portal.')

@section('actions')
  <a class="button" href="/admin/accounts/create">Crear account</a>
@endsection

@section('content')
  <div class="stack">
    @forelse($accounts as $a)
      <a class="card card-row" href="/admin/accounts/{{ $a->id }}">
        <div>
          <h3>{{ $a->name }}</h3>
          <p class="muted small">ID #{{ $a->id }} · {{ $a->billing_email ?? 'Sin email de facturacion' }}</p>
        </div>
        <span class="badge {{ $a->status === 'active' ? 'badge-success' : 'badge-warning' }}">
          {{ $a->status }}
        </span>
      </a>
    @empty
      <div class="card">
        <h3>Sin accounts</h3>
        <p class="muted">Crea el primer account para empezar a operar.</p>
      </div>
    @endforelse
  </div>
@endsection
