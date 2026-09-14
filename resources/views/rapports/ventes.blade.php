@extends('layouts.app')
@section('title','Rapport Ventes')
@section('page-title','Rapport des Ventes')

@section('content')
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Date début</label>
                <input type="date" name="date_debut" class="form-control" value="{{ $debut }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Date fin</label>
                <input type="date" name="date_fin" class="form-control" value="{{ $fin }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">&nbsp;</label>
                <button class="btn btn-primary w-100 d-block">Filtrer</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <div class="fw-bold fs-5 text-success">{{ number_format($totalVentes, 0, ',', ' ') }} F</div>
            <div class="text-muted small">Chiffre d'affaires</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <div class="fw-bold fs-4 text-primary">{{ $nbTransactions }}</div>
            <div class="text-muted small">Transactions</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <div class="fw-bold fs-5 text-warning">{{ $nbTransactions > 0 ? number_format($totalVentes / $nbTransactions, 0, ',', ' ') : 0 }} F</div>
            <div class="text-muted small">Panier moyen</div>
        </div>
    </div>
</div>

<!-- Top articles -->
@if($topArticles->count())
<div class="card mb-3">
    <div class="card-header bg-white py-3 fw-semibold"><i class="bi bi-trophy text-warning me-2"></i>Top articles vendus</div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>#</th><th>Article</th><th>Référence</th><th>Qté vendue</th><th>Chiffre d'affaires</th></tr></thead>
            <tbody>
            @foreach($topArticles as $i => $item)
            <tr>
                <td class="fw-bold text-warning">{{ $i + 1 }}</td>
                <td class="fw-semibold">{{ $item->nom }}</td>
                <td class="text-muted small">{{ $item->reference }}</td>
                <td class="fw-bold text-primary">{{ $item->total_vendu }}</td>
                <td class="fw-bold text-success">{{ number_format($item->chiffre_affaires, 0, ',', ' ') }} F</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@endif

<!-- Détail ventes -->
<div class="card">
    <div class="card-header bg-white py-3 fw-semibold"><i class="bi bi-list-ul me-2"></i>Détail des ventes</div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>N° Sortie</th><th>Date</th><th>Client</th><th>Type</th><th>Montant</th><th>Remise</th><th>Net</th><th>Par</th></tr></thead>
            <tbody>
            @forelse($sorties as $s)
            <tr>
                <td class="fw-semibold text-danger small">
                    <a href="{{ route('sorties.show', $s) }}" class="text-danger">{{ $s->numero }}</a>
                </td>
                <td class="small">{{ $s->date_sortie->format('d/m/Y') }}</td>
                <td class="small">{{ $s->client_nom ?? 'Comptant' }}</td>
                <td><span class="badge bg-light text-dark small">{{ ucfirst($s->type) }}</span></td>
                <td class="fw-semibold">{{ number_format($s->montant_total, 0, ',', ' ') }} F</td>
                <td class="text-warning">{{ $s->remise > 0 ? number_format($s->remise, 0, ',', ' ') . ' F' : '-' }}</td>
                <td class="fw-bold text-success">{{ number_format($s->montant_total - $s->remise, 0, ',', ' ') }} F</td>
                <td class="small">{{ $s->user->name ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center py-4 text-muted">Aucune vente sur cette période</td></tr>
            @endforelse
            </tbody>
            @if($sorties->count())
            <tfoot class="table-light">
                <tr>
                    <td colspan="6" class="text-end fw-bold">TOTAL</td>
                    <td class="fw-bold text-success">{{ number_format($totalVentes, 0, ',', ' ') }} F</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
        </div>
    </div>
</div>
<div class="mt-3">
    <a href="{{ route('rapports.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour aux rapports
    </a>
</div>
@endsection