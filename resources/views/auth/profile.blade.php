@extends('layouts.app')
@section('title', 'Mon profil')
@section('page-title', 'Informations personnelles')

@section('content')
<div class="row justify-content-center" style="min-height: calc(100vh - 160px);">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-person-badge fs-4 text-primary"></i>
                    <h5 class="mb-0 fw-semibold">Détails du compte</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100 bg-light">
                            <div class="text-muted small text-uppercase mb-1">Nom complet</div>
                            <div class="fw-bold fs-5">{{ $user->name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100 bg-light">
                            <div class="text-muted small text-uppercase mb-1">Adresse email</div>
                            <div class="fw-bold">{{ $user->email }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100 bg-light">
                            <div class="text-muted small text-uppercase mb-1">Téléphone</div>
                            <div class="fw-bold">{{ $user->telephone ?? 'Non renseigné' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100 bg-light">
                            <div class="text-muted small text-uppercase mb-1">Rôle</div>
                            <div>
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger">Administrateur</span>
                                @elseif($user->role === 'gerant')
                                    <span class="badge bg-primary">Gérant</span>
                                @else
                                    <span class="badge bg-secondary">Vendeur</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100 bg-light">
                            <div class="text-muted small text-uppercase mb-1">Statut du compte</div>
                            <div>
                                @if($user->actif)
                                    <span class="badge badge-normal">Actif</span>
                                @else
                                    <span class="badge badge-rupture">Inactif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100 bg-light">
                            <div class="text-muted small text-uppercase mb-1">Membre depuis</div>
                            <div class="fw-semibold">{{ $user->created_at->format('d/m/Y \à H:i') }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="text-muted small text-uppercase mb-1">Dernière modification</div>
                            <div class="fw-semibold">{{ $user->updated_at->format('d/m/Y \à H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire de changement de mot de passe -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-lock fs-4 text-warning"></i>
                    <h5 class="mb-0 fw-semibold">Modifier le mot de passe</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.update-password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Mot de passe actuel</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            @error('current_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nouveau mot de passe</label>
                            <input type="password" name="password" class="form-control" required>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmer le nouveau mot de passe</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Mettre à jour le mot de passe
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Retour au tableau de bord</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection