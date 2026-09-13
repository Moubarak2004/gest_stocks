@extends('layouts.app')
@section('title', 'Modifier sortie ' . $sortie->numero)
@section('page-title', 'Modifier sortie ' . $sortie->numero)

@section('content')
<div class="row justify-content-center">
<div class="col-lg-10">
<div class="card">
<div class="card-header bg-white py-3">
    <i class="bi bi-pencil-square me-2 text-warning"></i>Modification de la sortie
</div>
<div class="card-body">
<form action="{{ route('sorties.update', $sortie) }}" method="POST">
@csrf
@method('PUT')

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label">N° Sortie</label>
        <input type="text" class="form-control bg-light" value="{{ $sortie->numero }}" readonly>
    </div>
    <div class="col-md-3">
        <label class="form-label">Date <span class="text-danger">*</span></label>
        <input type="date" name="date_sortie" class="form-control" value="{{ $sortie->date_sortie->format('Y-m-d') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Type</label>
        <input type="text" class="form-control bg-light" value="{{ ucfirst(str_replace('_', ' ', $sortie->type)) }}" readonly>
    </div>
    <div class="col-md-3">
        <label class="form-label">Client</label>
        <input type="text" name="client_nom" class="form-control" value="{{ $sortie->client_nom }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Téléphone client</label>
        <input type="text" name="client_telephone" class="form-control" value="{{ $sortie->client_telephone }}">
    </div>
</div>

<div class="card border mb-3">
    <div class="card-header bg-light py-2 fw-semibold">Articles sortis (non modifiables)</div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr><th>Article</th><th>Référence</th><th>Qté</th><th>Prix unit.</th><th>Montant</th></tr>
            </thead>
            <tbody>
            @foreach($sortie->details as $detail)
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
                    <td class="fw-bold text-primary">{{ number_format($sortie->montant_total, 0, ',', ' ') }} F</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Notes</label>
    <textarea name="notes" class="form-control" rows="3">{{ $sortie->notes }}</textarea>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>Enregistrer les modifications
    </button>
    <a href="{{ route('sorties.show', $sortie) }}" class="btn btn-outline-secondary">Annuler</a>
</div>
</form>
</div>
</div>
</div>
</div>
@endsection