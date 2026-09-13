@extends('layouts.app')
@section('title','Nouvel utilisateur')
@section('page-title','Nouvel utilisateur')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
<div class="card-header bg-white py-3"><i class="bi bi-person-plus me-2 text-primary"></i>Créer un compte utilisateur</div>
<div class="card-body">
<form action="{{ route('users.store') }}" method="POST">
@csrf
<div class="row g-3">
    <div class="col-12">
        <label class="form-label">Nom complet <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>
    <div class="col-12">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
    </div>
    <div class="col-12">
        <label class="form-label">Téléphone</label>
        <input type="text" name="telephone" class="form-control" value="{{ old('telephone') }}">
    </div>
    <div class="col-12">
        <label class="form-label">Rôle <span class="text-danger">*</span></label>
        <select name="role" class="form-select" required>
            <option value="vendeur" {{ old('role') === 'vendeur' ? 'selected' : '' }}>Vendeur</option>
            <option value="gerant"  {{ old('role') === 'gerant'  ? 'selected' : '' }}>Gérant</option>
            <option value="admin"   {{ old('role') === 'admin'   ? 'selected' : '' }}>Administrateur</option>
        </select>
        <div class="form-text">
            Admin = accès total | Gérant = gestion stock | Vendeur = ventes uniquement
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">Mot de passe <span class="text-danger">*</span></label>
        <input type="password" name="password" class="form-control" required minlength="6">
        <div class="form-text">Minimum 6 caractères</div>
    </div>
    <div class="col-12">
        <label class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>
</div>
<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Créer</button>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
</form>
</div>
</div>
</div>
</div>
@endsection
