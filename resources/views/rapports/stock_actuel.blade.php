@extends('layouts.app')
@section('title','Stock Actuel')
@section('page-title','Rapport — Stock Actuel')

@section('content')
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <select name="categorie" class="form-select">
                    <option value="">Toutes catégories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('categorie') == $cat->id ? 'selected' : '' }}>{{ $cat->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="statut" class="form-select">
                    <option value="">Tous statuts</option>
                    <option value="normal"  {{ request('statut') === 'normal'  ? 'selected' : '' }}>Normal</option>
                    <option value="alerte"  {{ request('statut') === 'alerte'  ? 'selected' : '' }}>En alerte</option>
                    <option value="rupture" {{ request('statut') === 'rupture' ? 'selected' : '' }}>Rupture</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary flex-fill">Filtrer</button>
                <a href="{{ route('rapports.stock') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <div class="fw-bold fs-4 text-primary">{{ $articles->count() }}</div>
            <div class="text-muted small">Articles</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <div class="fw-bold fs-5 text-success">{{ number_format($valeurTotale, 0, ',', ' ') }} F</div>
            <div class="text-muted small">Valeur totale stock</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <div class="fw-bold fs-4 text-danger">{{ $articles->where('quantite_stock', '<=', 0)->count() }}</div>
            <div class="text-muted small">Ruptures</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center">
            <div class="fw-bold fs-4 text-warning">{{ $articles->filter(fn($a) => $a->estEnAlerte())->count() }}</div>
            <div class="text-muted small">En alerte</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between">
        <span class="fw-semibold"><i class="bi bi-table me-2"></i>Détail du stock</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Référence</th><th>Article</th><th>Catégorie</th><th>Fournisseur</th><th>Stock</th><th>Seuil</th><th>Prix achat</th><th>Prix vente</th><th>Valeur stock</th><th>Statut</th></tr>
                </thead>
                <tbody>
                @forelse($articles as $article)
                <tr>
                    <td class="small text-muted">{{ $article->reference }}</td>
                    <td class="fw-semibold">{{ $article->nom }}</td>
                    <td><span class="badge bg-light text-dark small">{{ $article->categorie->nom ?? '-' }}</span></td>
                    <td class="small">{{ $article->fournisseur->nom ?? '-' }}</td>
                    <td class="fw-bold {{ $article->quantite_stock <= 0 ? 'text-danger' : ($article->estEnAlerte() ? 'text-warning' : 'text-success') }}">
                        {{ $article->quantite_stock }} {{ $article->unite }}
                    </td>
                    <td class="text-muted small">{{ $article->stock_minimum }}</td>
                    <td class="small">{{ number_format($article->prix_achat, 0, ',', ' ') }} F</td>
                    <td class="small">{{ number_format($article->prix_vente, 0, ',', ' ') }} F</td>
                    <td class="fw-semibold text-primary small">{{ number_format($article->valeur_stock, 0, ',', ' ') }} F</td>
                    <td>
                        @if($article->statut_stock === 'rupture') <span class="badge badge-rupture">Rupture</span>
                        @elseif($article->statut_stock === 'alerte') <span class="badge badge-alerte">Alerte</span>
                        @else <span class="badge badge-normal">Normal</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center py-4 text-muted">Aucun article</td></tr>
                @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="8" class="text-end fw-bold">VALEUR TOTALE DU STOCK</td>
                        <td class="fw-bold text-primary">{{ number_format($valeurTotale, 0, ',', ' ') }} F</td>
                        <td></td>
                    </tr>
                </tfoot>
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

