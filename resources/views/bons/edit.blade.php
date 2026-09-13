@extends('layouts.app')
@section('title','Modifier bon de commande')
@section('page-title','Modifier bon ' . $bon->numero)

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">
<div class="card-header bg-white py-3"><i class="bi bi-pencil me-2 text-primary"></i>Modifier le bon {{ $bon->numero }}</div>
<div class="card-body">
<form action="{{ route('bons.update', $bon) }}" method="POST">
@csrf @method('PUT')
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Fournisseur</label>
        <select name="fournisseur_id" class="form-select">
            <option value="">-- Aucun --</option>
            @foreach($fournisseurs as $f)
            <option value="{{ $f->id }}" {{ $bon->fournisseur_id == $f->id ? 'selected' : '' }}>{{ $f->nom }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Statut</label>
        <select name="statut" class="form-select">
            <option value="brouillon" {{ $bon->statut === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
            <option value="envoyé"   {{ $bon->statut === 'envoyé'   ? 'selected' : '' }}>Envoyé</option>
            <option value="annulé"   {{ $bon->statut === 'annulé'   ? 'selected' : '' }}>Annulé</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Date commande</label>
        <input type="date" name="date_commande" class="form-control" value="{{ $bon->date_commande->format('Y-m-d') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Livraison prévue</label>
        <input type="date" name="date_livraison_prevue" class="form-control"
               value="{{ $bon->date_livraison_prevue ? $bon->date_livraison_prevue->format('Y-m-d') : '' }}">
    </div>
    <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="3">{{ $bon->notes }}</textarea>
    </div>
</div>

<!-- Articles (lecture seule) -->
<div class="card border mt-3">
    <div class="card-header bg-light py-2 small fw-semibold">Articles (non modifiables après création)</div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead><tr><th>Article</th><th>Qté</th><th>Prix unit.</th><th>Montant</th></tr></thead>
            <tbody>
            @foreach($bon->details as $d)
            <tr>
                <td>{{ $d->article->nom ?? '-' }}</td>
                <td>{{ $d->quantite_commandee }}</td>
                <td>{{ number_format($d->prix_unitaire, 0, ',', ' ') }} F</td>
                <td>{{ number_format($d->montant, 0, ',', ' ') }} F</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
    <a href="{{ route('bons.show', $bon) }}" class="btn btn-outline-secondary">Annuler</a>
</div>
</form>
</div>
</div>
</div>
</div>
@endsection
