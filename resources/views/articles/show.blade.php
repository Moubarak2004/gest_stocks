@extends('layouts.app')
@section('title','Détail article')
@section('page-title', $article->nom)

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body text-center py-4">
                @if($article->image)
                    <img src="{{ Storage::url($article->image) }}" class="img-fluid rounded mb-3" style="max-height:150px">
                @else
                    <div class="bg-light rounded d-inline-flex align-items-center justify-content-center mb-3" style="width:100px;height:100px">
                        <i class="bi bi-box2 fs-1 text-muted"></i>
                    </div>
                @endif
                <h5 class="fw-bold">{{ $article->nom }}</h5>
                <div class="badge bg-secondary mb-2">{{ $article->reference }}</div>
                <br>
                @if($article->statut_stock === 'rupture')
                    <span class="badge badge-rupture px-3 py-2">Rupture de stock</span>
                @elseif($article->statut_stock === 'alerte')
                    <span class="badge badge-alerte px-3 py-2">Stock faible</span>
                @else
                    <span class="badge badge-normal px-3 py-2">Stock normal</span>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-white py-3">Informations</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><td class="text-muted">Catégorie</td><td>{{ $article->categorie->nom ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Fournisseur</td><td>{{ $article->fournisseur->nom ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Unité</td><td>{{ $article->unite }}</td></tr>
                    <tr><td class="text-muted">Emplacement</td><td>{{ $article->emplacement ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Prix achat</td><td class="fw-semibold">{{ number_format($article->prix_achat, 0, ',', ' ') }} F</td></tr>
                    <tr><td class="text-muted">Prix vente</td><td class="fw-semibold text-success">{{ number_format($article->prix_vente, 0, ',', ' ') }} F</td></tr>
                    <tr><td class="text-muted">Prix détail</td><td>{{ number_format($article->prix_vente_detail, 0, ',', ' ') }} F</td></tr>
                    <tr><td class="text-muted">Stock actuel</td>
                        <td class="fw-bold {{ $article->quantite_stock <= 0 ? 'text-danger' : 'text-success' }}">
                            {{ $article->quantite_stock }} {{ $article->unite }}
                        </td>
                    </tr>
                    <tr><td class="text-muted">Seuil alerte</td><td>{{ $article->stock_minimum }} {{ $article->unite }}</td></tr>
                    <tr><td class="text-muted">Valeur stock</td><td class="fw-semibold text-primary">{{ number_format($article->valeur_stock, 0, ',', ' ') }} F</td></tr>
                </table>
            </div>
        </div>
        <div class="mt-3 d-flex gap-2">
            <a href="{{ route('articles.edit', $article) }}" class="btn btn-primary flex-fill">
                <i class="bi bi-pencil me-1"></i>Modifier
            </a>
        </div>
    </div>


    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3">
                <i class="bi bi-clock-history me-2 text-info"></i>Historique des mouvements
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Date</th><th>Type</th><th>Quantité</th><th>Avant</th><th>Après</th><th>Référence</th><th>Par</th></tr>
                    </thead>
                    <tbody>
                    @forelse($mouvements as $mvt)
                    <tr>
                        <td class="small text-muted">{{ $mvt->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($mvt->type === 'entree') <span class="badge badge-normal">Entrée</span>
                            @elseif($mvt->type === 'sortie') <span class="badge badge-rupture">Sortie</span>
                            @elseif($mvt->type === 'ajustement') <span class="badge bg-warning text-dark">Ajust.</span>
                            @else <span class="badge bg-secondary text-white">Inventaire</span>
                            @endif
                        </td>
                        <td class="{{ $mvt->quantite > 0 ? 'text-success' : 'text-danger' }} fw-bold">
                            {{ $mvt->quantite > 0 ? '+' : '' }}{{ $mvt->quantite }}
                        </td>
                        <td>{{ $mvt->stock_avant }}</td>
                        <td class="fw-semibold">{{ $mvt->stock_apres }}</td>
                        <td class="small">{{ $mvt->reference_document ?? $mvt->motif ?? '-' }}</td>
                        <td class="small">{{ $mvt->user->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-3">Aucun mouvement</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="px-3 py-2">{{ $mouvements->links() }}</div>
            </div>
        </div>
    </div>
</div>
<div class="mt-3">
    <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour à la liste
    </a>
</div>
@endsection
