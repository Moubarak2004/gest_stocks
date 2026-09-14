@extends('layouts.app')
@section('title', 'Entrée ' . $entree->numero)
@section('page-title', 'Entrée ' . $entree->numero)

@section('content')
<div class="row">
<div class="col-lg-4">
    <div class="card mb-3">
        <div class="card-header bg-white py-3 fw-semibold"><i class="bi bi-info-circle me-2"></i>Informations</div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table table-sm mb-0">
                <tr><td class="text-muted">N° Entrée</td><td class="fw-bold text-success">{{ $entree->numero }}</td></tr>
                <tr><td class="text-muted">Date</td><td>{{ $entree->date_entree->format('d/m/Y') }}</td></tr>
                <tr><td class="text-muted">Fournisseur</td><td>{{ $entree->fournisseur->nom ?? 'N/A' }}</td></tr>
                <tr><td class="text-muted">Réf. bon</td><td>{{ $entree->reference_bon ?? '-' }}</td></tr>
                <tr><td class="text-muted">Montant total</td><td class="fw-bold text-primary">{{ number_format($entree->montant_total, 0, ',', ' ') }} F</td></tr>
                <tr><td class="text-muted">Statut</td>
                    <td>@if($entree->statut === 'validé')<span class="badge badge-normal">Validé</span>
                        @elseif($entree->statut === 'annulé')<span class="badge badge-rupture">Annulé</span>
                        @else<span class="badge bg-secondary text-white">Brouillon</span>@endif
                    </td>
                </tr>
                <tr><td class="text-muted">Créé par</td><td>{{ $entree->user->name ?? '-' }}</td></tr>
                <tr><td class="text-muted">Date saisie</td><td>{{ $entree->created_at->format('d/m/Y H:i') }}</td></tr>
            </table>
            </div>
        </div>
    </div>
    @if($entree->notes)
    <div class="card">
        <div class="card-body"><strong>Notes :</strong> {{ $entree->notes }}</div>
    </div>
    @endif
</div>
<div class="col-lg-8">
    <div class="card">
        <div class="card-header bg-white py-3 fw-semibold"><i class="bi bi-list-ul me-2"></i>Articles reçus</div>
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Article</th><th>Référence</th><th>Qté</th><th>Prix unit.</th><th>Montant</th></tr></thead>
                <tbody>
                @foreach($entree->details as $d)
                <tr>
                    <td class="fw-semibold">{{ $d->article->nom ?? '-' }}</td>
                    <td class="text-muted small">{{ $d->article->reference ?? '-' }}</td>
                    <td class="fw-bold text-success">{{ $d->quantite }} {{ $d->article->unite ?? '' }}</td>
                    <td>{{ number_format($d->prix_unitaire, 0, ',', ' ') }} F</td>
                    <td class="fw-semibold text-primary">{{ number_format($d->montant, 0, ',', ' ') }} F</td>
                </tr>
                @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="4" class="text-end fw-bold">TOTAL</td>
                        <td class="fw-bold text-primary fs-6">{{ number_format($entree->montant_total, 0, ',', ' ') }} F</td>
                    </tr>
                </tfoot>
            </table>
            </div>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <a href="{{ route('entrees.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour</a>

        @if($entree->statut === 'brouillon')
            <form action="{{ route('entrees.valider', $entree) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Valider cette entrée ? Le stock sera mis à jour.')">
                    <i class="bi bi-check-circle me-1"></i> Valider l'entrée
                </button>
            </form>
            <a href="{{ route('entrees.edit', $entree) }}" class="btn btn-outline-warning">
                <i class="bi bi-pencil me-1"></i> Modifier
            </a>
        @endif

        @if($entree->statut === 'validé')
            <form action="{{ route('entrees.annuler', $entree) }}" method="POST" onsubmit="return confirm('Annuler cette entrée ? Le stock sera mis à jour.')">
                @csrf
                <button class="btn btn-outline-danger"><i class="bi bi-x-circle me-1"></i>Annuler l'entrée</button>
            </form>
        @endif
    </div>
</div>
</div>
<a href="{{ route('entrees.pdf', $entree) }}" class="btn btn-sm btn-primary mt-2"><i class="bi bi-printer"></i> PDF</a>
@endsection