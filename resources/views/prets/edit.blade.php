@extends('layouts.app')
@section('title', 'Modifier prêt ' . $pret->numero)
@section('page-title', 'Modifier prêt ' . $pret->numero)

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">
<div class="card-header bg-white py-3">
    <i class="bi bi-pencil-square me-2 text-warning"></i>Modification du prêt
</div>
<div class="card-body">
<form action="{{ route('prets.update', $pret) }}" method="POST">
@csrf
@method('PUT')

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">N° Prêt</label>
        <input type="text" class="form-control bg-light" value="{{ $pret->numero }}" readonly>
    </div>
    <div class="col-md-4">
        <label class="form-label">Date du prêt <span class="text-danger">*</span></label>
        <input type="date" name="date_pret" class="form-control" value="{{ $pret->date_pret->format('Y-m-d') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Retour prévu</label>
        <input type="date" name="date_retour_prevue" class="form-control" value="{{ $pret->date_retour_prevue ? $pret->date_retour_prevue->format('Y-m-d') : '' }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Article</label>
        <input type="text" class="form-control bg-light" value="{{ $pret->article->nom }} ({{ $pret->article->reference }}) - Qté: {{ $pret->quantite }} {{ $pret->article->unite }}" readonly>
    </div>

    <div class="col-md-6">
        <label class="form-label">Emprunteur <span class="text-danger">*</span></label>
        <input type="text" name="emprunteur_nom" class="form-control" value="{{ $pret->emprunteur_nom }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Téléphone</label>
        <input type="text" name="emprunteur_telephone" class="form-control" value="{{ $pret->emprunteur_telephone }}">
    </div>

    <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="3">{{ $pret->notes }}</textarea>
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>Enregistrer les modifications
    </button>
    <a href="{{ route('prets.show', $pret) }}" class="btn btn-outline-secondary">Annuler</a>
</div>
</form>
</div>
</div>
</div>
</div>
@endsection