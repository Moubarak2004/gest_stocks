@extends('layouts.app')
@section('title', 'Sortie ' . $sortie->numero)
@section('page-title', 'Sortie ' . $sortie->numero)

@section('content')
<div class="row">
<div class="col-lg-4">
    <div class="card mb-3">
        <div class="card-header bg-white py-3 fw-semibold"><i class="bi bi-info-circle me-2"></i>Informations</div>
        <div class="card-body">
            <table class="table table-sm mb-0">
                <tr><td class="text-muted">N° Sortie</td><td class="fw-bold text-danger">{{ $sortie->numero }}</td></tr>
                <tr><td class="text-muted">Date</td><td>{{ optional($sortie->date_sortie)->format('d/m/Y') ?? 'Non définie' }}</td></tr>
                <tr><td class="text-muted">Type</td><td>{{ ucfirst(str_replace('_', ' ', $sortie->type)) }}</td></tr>
                <tr><td class="text-muted">Client</td><td>{{ $sortie->client_nom ?? 'Comptant' }}</td></tr>
                <tr><td class="text-muted">Téléphone</td><td>{{ $sortie->client_telephone ?? '-' }}</td></tr>
                <tr><td class="text-muted">Sous-total</td><td>{{ number_format($sortie->montant_total, 0, ',', ' ') }} F</td></tr>
                <tr><td class="text-muted">Remise</td><td class="text-warning">{{ number_format($sortie->remise, 0, ',', ' ') }} F</td></tr>
                <tr><td class="text-muted">Net à payer</td><td class="fw-bold text-primary fs-6">{{ number_format($sortie->montant_total - $sortie->remise, 0, ',', ' ') }} F</td></tr>
                <tr><td class="text-muted">Reçu</td><td class="text-success">{{ number_format($sortie->montant_recu, 0, ',', ' ') }} F</td></tr>
                <tr><td class="text-muted">Monnaie</td><td class="fw-bold text-success">{{ number_format($sortie->monnaie, 0, ',', ' ') }} F</td></tr>
                <tr><td class="text-muted">Statut</td>
                    <td>@if($sortie->statut === 'validé')<span class="badge badge-normal">Validé</span>
                        @elseif($sortie->statut === 'annulé')<span class="badge badge-rupture">Annulé</span>
                        @else<span class="badge bg-secondary text-white">Brouillon</span>@endif
                    </td>
                </tr>
                <tr><td class="text-muted">Par</td><td>{{ $sortie->user->name ?? '-' }}</td></tr>
            </table>
        </div>
    </div>
</div>
<div class="col-lg-8">
    <div class="card">
        <div class="card-header bg-white py-3 fw-semibold"><i class="bi bi-list-ul me-2"></i>Articles sortis</div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Article</th><th>Réf.</th><th>Qté</th><th>Prix unit.</th><th>Montant</th></tr></thead>
                <tbody>
                @foreach($sortie->details as $d)
                <tr>
                    <td class="fw-semibold">{{ $d->article->nom ?? '-' }}</td>
                    <td class="text-muted small">{{ $d->article->reference ?? '-' }}</td>
                    <td class="fw-bold text-danger">{{ $d->quantite }} {{ $d->article->unite ?? '' }}</td>
                    <td>{{ number_format($d->prix_unitaire, 0, ',', ' ') }} F</td>
                    <td class="fw-semibold text-danger">{{ number_format($d->montant, 0, ',', ' ') }} F</td>
                </tr>
                @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="4" class="text-end fw-bold">NET À PAYER</td>
                        <td class="fw-bold text-primary fs-6">{{ number_format($sortie->montant_total - $sortie->remise, 0, ',', ' ') }} F</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <a href="{{ route('sorties.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour</a>

        @if($sortie->statut === 'brouillon')
            <form action="{{ route('sorties.valider', $sortie) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Valider cette sortie ? Le stock sera mis à jour.')">
                    <i class="bi bi-check-circle me-1"></i> Valider la sortie
                </button>
            </form>
            <a href="{{ route('sorties.edit', $sortie) }}" class="btn btn-outline-warning">
                <i class="bi bi-pencil me-1"></i> Modifier
            </a>
        @endif

        @if($sortie->statut === 'validé')
            <form action="{{ route('sorties.annuler', $sortie) }}" method="POST" onsubmit="return confirm('Annuler cette sortie ?')">
                @csrf
                <button class="btn btn-outline-danger"><i class="bi bi-x-circle me-1"></i>Annuler</button>
            </form>
        @endif
    </div>
</div>
</div>
<a href="{{ route('sorties.pdf', $sortie) }}" class="btn btn-sm btn-primary mt-2"><i class="bi bi-printer"></i> PDF</a>
@endsection