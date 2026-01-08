@extends('landlord.admin.layout')

@section('eyebrow', 'Licencias')
@section('title', 'Editar licencia')
@section('subtitle', 'Ajusta precio, vigencia y condiciones de la licencia.')

@section('actions')
  <a class="button-secondary" href="/admin/accounts/{{ $account->id }}">Volver</a>
@endsection

@section('content')
  @php
    $startsAtValue = $license->starts_at
      ? \Carbon\CarbonImmutable::parse($license->starts_at)->format('Y-m-d\\TH:i')
      : '';
  @endphp
  <form method="POST" action="/admin/accounts/{{ $account->id }}/licenses/{{ $license->id }}" class="card form">
    @csrf
    @method('PUT')
    <div class="field">
      <label for="plan_id">Plan</label>
      <select id="plan_id" name="plan_id" required>
        @foreach($plans as $plan)
          <option value="{{ $plan->id }}" {{ (string) old('plan_id', $license->plan_id) === (string) $plan->id ? 'selected' : '' }}>
            {{ $plan->name }} ({{ number_format((float) $plan->price_usd, 2) }} USD · {{ $plan->max_tenants }} tiendas)
          </option>
        @endforeach
      </select>
    </div>
    <div class="field">
      <label for="starts_at">Inicio</label>
      <input id="starts_at" name="starts_at" type="datetime-local" required value="{{ old('starts_at', $startsAtValue) }}">
    </div>
    <div class="field">
      <label for="price_usd">Precio USD (mensual)</label>
      <input id="price_usd" name="price_usd" type="number" step="0.01" min="0.01" required value="{{ old('price_usd', $license->price_usd ?? $license->amount) }}">
    </div>
    <div class="field">
      <label for="currency">Moneda</label>
      <input id="currency" name="currency" type="text" maxlength="3" required value="{{ old('currency', $license->currency) }}">
    </div>
    <div class="field">
      <label for="contract_months">Meses de contrato</label>
      <input id="contract_months" name="contract_months" type="number" min="1" required value="{{ old('contract_months', $license->contract_months) }}">
    </div>
    <div class="field">
      <label for="grace_days">Dias de gracia</label>
      <input id="grace_days" name="grace_days" type="number" min="0" required value="{{ old('grace_days', $license->grace_days) }}">
    </div>
    <div class="field">
      <label for="max_tenants">Maximo de tiendas</label>
      <input id="max_tenants" name="max_tenants" type="number" min="1" required value="{{ old('max_tenants', $license->max_tenants) }}">
    </div>
    <div class="field">
      <label for="status">Estado</label>
      <select id="status" name="status" required>
        @foreach(['active' => 'Active', 'grace' => 'Grace', 'suspended' => 'Suspended', 'expired' => 'Expired', 'cancelled' => 'Cancelled'] as $value => $label)
          <option value="{{ $value }}" {{ old('status', $license->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="card-actions">
      <button type="submit" class="button">Guardar cambios</button>
    </div>
  </form>
@endsection
