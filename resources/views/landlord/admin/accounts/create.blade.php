@extends('landlord.admin.layout')

@section('title', 'Crear account')
@section('subtitle', 'Registra un nuevo cliente para asignar licencias y usuarios.')

@section('actions')
  <a class="button-secondary" href="/admin/accounts">Volver</a>
@endsection

@section('content')
  <form method="POST" action="/admin/accounts" class="card form">
    @csrf
    <div class="field">
      <label for="name">Nombre</label>
      <input id="name" name="name" required>
    </div>
    <div class="field">
      <label for="billing_email">Email facturacion (opcional)</label>
      <input id="billing_email" name="billing_email" type="email">
    </div>
    <div class="card-actions">
      <button type="submit" class="button">Crear account</button>
    </div>
  </form>
@endsection
