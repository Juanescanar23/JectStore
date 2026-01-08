@extends('landlord.admin.layout')

@section('title', 'Crear licencia')
@section('subtitle', 'Contrato de 12 meses para el account seleccionado.')

@section('actions')
  <a class="button-secondary" href="/admin/accounts/{{ $account->id }}">Volver</a>
@endsection

@section('content')
  <form method="POST" action="/admin/accounts/{{ $account->id }}/licenses" class="card form">
    @csrf
    <div class="field">
      <label for="starts_at">Inicio</label>
      <input id="starts_at" name="starts_at" type="datetime-local" required>
    </div>
    <div class="field">
      <label for="amount">Monto mensual</label>
      <input id="amount" name="amount" type="number" step="0.01" min="0" placeholder="0.00">
    </div>
    <div class="field">
      <label for="currency">Moneda (ISO)</label>
      <input id="currency" name="currency" type="text" maxlength="3" placeholder="USD">
    </div>
    <div class="card-actions">
      <button type="submit" class="button">Crear licencia</button>
    </div>
  </form>
@endsection
