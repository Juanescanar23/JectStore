@extends('landlord.admin.layout')

@section('eyebrow', 'Planes')
@section('title', 'Planes')
@section('subtitle', 'Define precios y condiciones comerciales para nuevas licencias.')

@section('actions')
  <a class="button" href="/admin/plans/create">Crear plan</a>
@endsection

@section('content')
  <section class="card">
    <div class="card-head">
      <h3>Lista de planes</h3>
      <span class="badge">Comercial</span>
    </div>
    <div class="list">
      @forelse($plans as $plan)
        <div class="list-row">
          <div>
            <strong>{{ $plan->name }}</strong>
            <p>{{ $plan->code }} · {{ number_format((float) $plan->price_usd, 2) }} USD · {{ $plan->max_tenants }} tiendas · {{ $plan->contract_months }} meses</p>
          </div>
          <div class="actions">
            <span class="badge {{ $plan->is_active ? 'badge-success' : 'badge-warning' }}">{{ $plan->is_active ? 'ACTIVE' : 'INACTIVE' }}</span>
            <a class="button-secondary" href="/admin/plans/{{ $plan->id }}/edit">Editar</a>
          </div>
        </div>
      @empty
        <div class="list-row">
          <div>
            <strong>Sin planes</strong>
            <p>Crea el primer plan para habilitar la venta de licencias.</p>
          </div>
        </div>
      @endforelse
    </div>
  </section>
@endsection
