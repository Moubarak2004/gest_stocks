@extends('layouts.app')
@section('title','Alertes Stock')
@section('page-title','Alertes Stock')

@section('content')
<!-- Résumé -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#ef4444,#f87171)">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="stat-value">{{ $articlesRupture->count() }}</div>
                    <div class="stat-label">Articles en rupture totale</div>
                </div>
                <i class="bi bi-x-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#fbbf24)">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="stat-value">{{ $articlesAlerte->count() }}</div>
                    <div class="stat-label">Articles sous le seuil</div>
                </div>
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#6366f1,#818cf8)">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="stat-value">{{ $alertes->where('lue', false)->count() }}</div>
                    <div class="stat-label">Alertes non lues</div>
                </div>
                <i class="bi bi-bell"></i>
            </div>
        </div>
    </div>
</div>

<!-- Actions -->
<div class="d-flex gap-2 mb-3">
    <form action="{{ route('alertes.toutes-lues') }}" method="POST">
        @csrf
        <button class="btn btn-outline-success btn-sm">
            <i class="bi bi-check-all me-1"></i>Tout marquer comme lu
        </button>
    </form>
    <form action="{{ route('alertes.generer') }}" method="POST">
        @csrf
        <button class="btn btn-outline-primary btn-sm">
            <i class="bi bi-arrow-clockwise me-1"></i>Actualiser les alertes
        </button>
    </form>
    <a href="{{ route('bons.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-file-earmark-plus me-1"></i>Créer bon de commande
    </a>
</div>

<!-- Articles en rupture -->
@if($articlesRupture->count())
<div class="card mb-3 border-danger">
    <div class="card-header bg-danger text-white py-3">
        <i class="bi bi-x-octagon me-2"></i>Articles en rupture de stock ({{ $articlesRupture->count() }})
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Article</th><th>Référence</th><th>Catégorie</th><th>Stock actuel</th><th>Seuil</th><th>Actions</th></tr></thead>
            <tbody>
            @foreach($articlesRupture as $art)
            <tr>
                <td class="fw-semibold">{{ $art->nom }}</td>
                <td class="text-muted small">{{ $art->reference }}</td>
                <td>{{ $art->categorie->nom ?? '-' }}</td>
                <td class="fw-bold text-danger">{{ $art->quantite_stock }} {{ $art->unite }}</td>
                <td>{{ $art->stock_minimum }}</td>
                <td>
                    <a href="{{ route('articles.show', $art) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                    <a href="{{ route('entrees.create') }}" class="btn btn-sm btn-outline-success"><i class="bi bi-plus-circle"></i> Entrée</a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@endif

<!-- Articles en alerte -->
@if($articlesAlerte->count())
<div class="card mb-3 border-warning">
    <div class="card-header bg-warning py-3">
        <i class="bi bi-exclamation-triangle me-2"></i>Articles sous le seuil d'alerte ({{ $articlesAlerte->count() }})
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Article</th><th>Référence</th><th>Stock</th><th>Seuil</th><th>Actions</th></tr></thead>
            <tbody>
            @foreach($articlesAlerte as $art)
            <tr>
                <td class="fw-semibold">{{ $art->nom }}</td>
                <td class="text-muted small">{{ $art->reference }}</td>
                <td class="fw-bold text-warning">{{ $art->quantite_stock }} {{ $art->unite }}</td>
                <td>{{ $art->stock_minimum }}</td>
                <td>
                    <a href="{{ route('articles.show', $art) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@endif

<!-- Historique alertes -->
<div class="card">
    <div class="card-header bg-white py-3 fw-semibold">
        <i class="bi bi-clock-history me-2"></i>Historique des alertes
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Article</th><th>Type</th><th>Stock</th><th>Seuil</th><th>Date</th><th>Statut</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($alertes as $alerte)
            <tr class="{{ $alerte->lue ? '' : 'table-warning' }}">
                <td class="fw-semibold">{{ $alerte->article->nom ?? '-' }}</td>
                <td>
                    @if($alerte->type === 'stock_epuise') <span class="badge badge-rupture">Épuisé</span>
                    @elseif($alerte->type === 'stock_minimum') <span class="badge badge-alerte">Seuil min.</span>
                    @else <span class="badge bg-secondary text-white">Péremption</span> @endif
                </td>
                <td class="fw-bold {{ $alerte->quantite_actuelle <= 0 ? 'text-danger' : 'text-warning' }}">
                    {{ $alerte->quantite_actuelle }}
                </td>
                <td>{{ $alerte->seuil_alerte }}</td>
                <td class="small text-muted">{{ $alerte->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    @if($alerte->lue)
                        <span class="badge badge-normal">Lu</span>
                    @else
                        <span class="badge badge-rupture">Non lu</span>
                    @endif
                </td>
                <td>
                    @if(!$alerte->lue)
                    <form action="{{ route('alertes.lue', $alerte) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-success"><i class="bi bi-check"></i> Lu</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-4 text-muted">
                <i class="bi bi-check-circle-fill text-success fs-2 d-block mb-2"></i>
                Aucune alerte enregistrée
            </td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
        <div class="px-3 py-2">{{ $alertes->links() }}</div>
    </div>
</div>
@endsection