@extends('landlord.admin.layout')

@section('eyebrow', 'Planes')
@section('title', 'Editar plan')
@section('subtitle', 'Ajusta precio, vigencia o condiciones comerciales.')

@section('actions')
  <a class="button-secondary" href="/admin/plans">Volver</a>
@endsection

@section('content')
  <form method="POST" action="/admin/plans/{{ $plan->id }}" class="card form">
    @csrf
    @method('PUT')
    <div class="field">
      <label for="name">Nombre</label>
      <input id="name" name="name" required value="{{ old('name', $plan->name) }}">
    </div>
    <div class="field">
      <label for="code">Codigo</label>
      <input id="code" name="code" required value="{{ old('code', $plan->code) }}">
    </div>
    <div class="field">
      <label for="price_usd">Precio USD (mensual)</label>
      <input id="price_usd" name="price_usd" type="number" step="0.01" min="0.01" required value="{{ old('price_usd', $plan->price_usd) }}">
    </div>
    <div class="field">
      <label for="contract_months">Meses de contrato</label>
      <input id="contract_months" name="contract_months" type="number" min="1" required value="{{ old('contract_months', $plan->contract_months) }}">
    </div>
    <div class="field">
      <label for="grace_days">Dias de gracia</label>
      <input id="grace_days" name="grace_days" type="number" min="0" required value="{{ old('grace_days', $plan->grace_days) }}">
    </div>
    <div class="field">
      <label for="max_tenants">Maximo de tiendas</label>
      <input id="max_tenants" name="max_tenants" type="number" min="1" required value="{{ old('max_tenants', $plan->max_tenants) }}">
    </div>
    <div class="field">
      <label for="features">Features (JSON)</label>
      <textarea id="features" name="features" rows="6">{{ old('features', $plan->features ? json_encode($plan->features) : '') }}</textarea>
    </div>
    <div class="field">
      <label>
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
        Plan activo
      </label>
    </div>
    <div class="card-actions">
      <button type="submit" class="button">Guardar cambios</button>
    </div>
  </form>
@endsection
