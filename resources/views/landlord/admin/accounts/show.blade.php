@extends('landlord.admin.layout')

@section('eyebrow', 'Account')
@section('title', $account->name)
@section('subtitle', 'Detalle de licencias y usuarios del portal.')

@section('actions')
  <a class="button-secondary" href="/admin/accounts">Volver</a>
@endsection

@section('content')
  <div class="card">
    <div class="meta">
      <div>
        <span>Status</span>
        <strong>{{ $account->status }}</strong>
      </div>
      <div>
        <span>Billing</span>
        <strong>{{ $account->billing_email ?? 'Sin email' }}</strong>
      </div>
    </div>
  </div>

  <div class="grid two">
    <section class="card">
      <div class="card-head">
        <h3>Licencias</h3>
        <a class="button-secondary" href="/admin/accounts/{{ $account->id }}/licenses/create">Crear licencia</a>
      </div>
      <div class="list">
        @forelse($licenses as $l)
          <div class="list-row">
            <div>
              <strong>{{ $l->plan_code }}</strong>
              <p>{{ $l->starts_at }} -> {{ $l->expires_at }} · max {{ $l->max_tenants }}</p>
            </div>
            <span class="badge {{ $l->status === 'active' ? 'badge-success' : 'badge-warning' }}">{{ $l->status }}</span>
          </div>
        @empty
          <div class="list-row">
            <div>
              <strong>Sin licencias</strong>
              <p>Agrega la primera licencia para este account.</p>
            </div>
          </div>
        @endforelse
      </div>
    </section>

    <section class="card">
      <div class="card-head">
        <h3>Usuarios portal</h3>
        <a class="button-secondary" href="/admin/accounts/{{ $account->id }}/users/create">Crear usuario comprador</a>
      </div>
      <div class="list">
        @forelse($users as $u)
          <div class="list-row">
            <div>
              <strong>{{ $u->name }}</strong>
              <p>{{ $u->email }} · {{ $u->role }}</p>
            </div>
            <span class="badge {{ $u->status === 'active' ? 'badge-success' : 'badge-warning' }}">{{ $u->status }}</span>
          </div>
        @empty
          <div class="list-row">
            <div>
              <strong>Sin usuarios</strong>
              <p>Invita el primer usuario para este account.</p>
            </div>
          </div>
        @endforelse
      </div>
    </section>
  </div>
@endsection
