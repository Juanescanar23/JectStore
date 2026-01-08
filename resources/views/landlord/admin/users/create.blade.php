@extends('landlord.admin.layout')

@section('title', 'Crear usuario portal')
@section('subtitle', 'Asigna un usuario comprador para este account.')

@section('actions')
  <a class="button-secondary" href="/admin/accounts/{{ $account->id }}">Volver</a>
@endsection

@section('content')
  <form method="POST" action="/admin/accounts/{{ $account->id }}/users" class="card form">
    @csrf
    <div class="field">
      <label for="name">Nombre</label>
      <input id="name" name="name" required>
    </div>
    <div class="field">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" required>
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>
    </div>
    <div class="field">
      <label for="role">Rol</label>
      <select id="role" name="role">
        <option value="account_owner">account_owner</option>
        <option value="account_manager">account_manager</option>
      </select>
    </div>
    <div class="card-actions">
      <button type="submit" class="button">Crear usuario</button>
    </div>
  </form>
@endsection
