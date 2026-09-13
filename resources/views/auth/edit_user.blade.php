@extends('layouts.app')
@section('title','Modifier utilisateur')
@section('page-title','Modifier ' . $user->name)

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
<div class="card-header bg-white py-3"><i class="bi bi-pencil me-2"></i>Modifier — {{ $user->name }}</div>
<div class="card-body">
<form action="{{ route('users.update', $user) }}" method="POST">
@csrf @method('PUT')
<div class="row g-3">
    <div class="col-12">
        <label class="form-label">Nom complet <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
    </div>
    <div class="col-12">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
    </div>
    <div class="col-12">
        <label class="form-label">Téléphone</label>
        <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $user->telephone) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Rôle <span class="text-danger">*</span></label>
        <select name="role" class="form-select" required>
            <option value="vendeur" {{ $user->role === 'vendeur' ? 'selected' : '' }}>Vendeur</option>
            <option value="gerant"  {{ $user->role === 'gerant'  ? 'selected' : '' }}>Gérant</option>
            <option value="admin"   {{ $user->role === 'admin'   ? 'selected' : '' }}>Administrateur</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Statut</label>
        <select name="actif" class="form-select">
            <option value="1" {{ $user->actif ? 'selected' : '' }}>Actif</option>
            <option value="0" {{ !$user->actif ? 'selected' : '' }}>Désactivé</option>
        </select>
    </div>
    <div class="col-12">
        <hr>
        <label class="form-label">Nouveau mot de passe <small class="text-muted">(laisser vide pour ne pas changer)</small></label>
        <input type="password" name="password" class="form-control" minlength="6">
    </div>
    <div class="col-12">
        <label class="form-label">Confirmer le nouveau mot de passe</label>
        <input type="password" name="password_confirmation" class="form-control">
    </div>
</div>
<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
</form>
</div>
</div>
</div>
</div>
@endsection