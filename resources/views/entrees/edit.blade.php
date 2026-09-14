@extends('layouts.app')
@section('title', 'Modifier entrée ' . $entree->numero)
@section('page-title', 'Modifier entrée ' . $entree->numero)

@section('content')
<div class="row justify-content-center">
<div class="col-lg-10">
<div class="card">
<div class="card-header bg-white py-3">
    <i class="bi bi-pencil-square me-2 text-warning"></i>Modification de l’entrée
</div>
<div class="card-body">
<form action="{{ route('entrees.update', $entree) }}" method="POST">
@csrf
@method('PUT')

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label">N° Entrée</label>
        <input type="text" class="form-control bg-light" value="{{ $entree->numero }}" readonly>
    </div>
    <div class="col-md-3">
        <label class="form-label">Date <span class="text-danger">*</span></label>
        <input type="date" name="date_entree" class="form-control" value="{{ $entree->date_entree->format('Y-m-d') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Fournisseur</label>
        <select name="fournisseur_id" class="form-select">
            <option value="">-- Aucun --</option>
            @foreach($fournisseurs as $f)
                <option value="{{ $f->id }}" {{ $entree->fournisseur_id == $f->id ? 'selected' : '' }}>{{ $f->nom }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Réf. bon livraison</label>
        <input type="text" name="reference_bon" class="form-control" value="{{ $entree->reference_bon }}">
    </div>
</div>

<div class="card border mb-3">
    <div class="card-header bg-light py-2 fw-semibold">Articles reçus (non modifiables)</div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr><th>Article</th><th>Référence</th><th>Qté</th><th>Prix unit.</th><th>Montant</th></tr>
            </thead>
            <tbody>
            @foreach($entree->details as $detail)
                <tr>
                    <td class="fw-semibold">{{ $detail->article->nom }}</td>
                    <td>{{ $detail->article->reference }}</td>
                    <td>{{ $detail->quantite }} {{ $detail->article->unite }}</td>
                    <td>{{ number_format($detail->prix_unitaire, 0, ',', ' ') }} F</td>
                    <td>{{ number_format($detail->montant, 0, ',', ' ') }} F</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr><td colspan="4" class="text-end fw-bold">Total</td>
                    <td class="fw-bold text-primary">{{ number_format($entree->montant_total, 0, ',', ' ') }} F</td>
                </tr>
            </tfoot>
        </table>
        </div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Notes</label>
    <textarea name="notes" class="form-control" rows="3">{{ $entree->notes }}</textarea>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>Enregistrer les modifications
    </button>
    <a href="{{ route('entrees.show', $entree) }}" class="btn btn-outline-secondary">Annuler</a>
</div>
</form>
</div>
</div>
</div>
</div>
@endsection