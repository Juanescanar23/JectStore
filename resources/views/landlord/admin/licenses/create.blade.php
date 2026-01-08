@extends('landlord.admin.layout')

@section('title', 'Crear licencia')
@section('subtitle', 'Asigna un plan comercial y fecha de inicio.')

@section('actions')
  <a class="button-secondary" href="/admin/accounts/{{ $account->id }}">Volver</a>
@endsection

@section('content')
  @if($plans->isEmpty())
    <div class="card">
      <p class="muted">Necesitas crear al menos un plan activo antes de generar licencias.</p>
      <div class="card-actions">
        <a class="button" href="/admin/plans/create">Crear plan</a>
      </div>
    </div>
  @else
    <form method="POST" action="/admin/accounts/{{ $account->id }}/licenses" class="card form">
      @csrf
      <div class="field">
        <label for="plan_id">Plan</label>
        <select id="plan_id" name="plan_id" required>
          <option value="">Selecciona un plan</option>
          @foreach($plans as $plan)
            <option value="{{ $plan->id }}" {{ (string) old('plan_id') === (string) $plan->id ? 'selected' : '' }}>
              {{ $plan->name }} ({{ number_format((float) $plan->price_usd, 2) }} USD · {{ $plan->max_tenants }} tiendas)
            </option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label for="starts_at">Inicio</label>
        <input id="starts_at" name="starts_at" type="datetime-local" required value="{{ old('starts_at') }}">
      </div>
      <div class="card-actions">
        <button type="submit" class="button">Crear licencia</button>
      </div>
    </form>
  @endif
@endsection
