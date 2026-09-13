@extends('layouts.app')
@section('title','Articles')
@section('page-title','Articles')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <a href="{{ route('articles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouvel article
    </a>
</div>

<!-- Filtres -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Nom, référence, code barre..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="categorie" class="form-select">
                    <option value="">Toutes catégories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('categorie') == $cat->id ? 'selected' : '' }}>{{ $cat->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="statut" class="form-select">
                    <option value="">Tous statuts</option>
                    <option value="normal"  {{ request('statut') == 'normal'  ? 'selected' : '' }}>Normal</option>
                    <option value="alerte"  {{ request('statut') == 'alerte'  ? 'selected' : '' }}>En alerte</option>
                    <option value="rupture" {{ request('statut') == 'rupture' ? 'selected' : '' }}>Rupture</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary flex-fill">Filtrer</button>
                <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Prix achat</th>
                        <th>Prix vente</th>
                        <th>Stock</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($articles as $article)
                <tr>
                    <td class="fw-semibold text-primary small">{{ $article->reference }}</td>
                    <td>
                        <div class="fw-semibold">{{ $article->nom }}</div>
                        @if($article->emplacement)<div class="text-muted small"><i class="bi bi-geo-alt"></i> {{ $article->emplacement }}</div>@endif
                    </td>
                    <td><span class="badge bg-light text-dark">{{ $article->categorie->nom ?? '-' }}</span></td>
                    <td>{{ number_format($article->prix_achat, 0, ',', ' ') }} F</td>
                    <td>{{ number_format($article->prix_vente, 0, ',', ' ') }} F</td>
                    <td class="fw-bold {{ $article->quantite_stock <= 0 ? 'text-danger' : ($article->quantite_stock <= $article->stock_minimum ? 'text-warning' : 'text-success') }}">
                        {{ $article->quantite_stock }} {{ $article->unite }}
                    </td>
                    <td>
                        @if($article->statut_stock === 'rupture')
                            <span class="badge badge-rupture px-2">Rupture</span>
                        @elseif($article->statut_stock === 'alerte')
                            <span class="badge badge-alerte px-2">Alerte</span>
                        @else
                            <span class="badge badge-normal px-2">Normal</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('articles.show', $article) }}" class="btn btn-outline-info" title="Détails"><i class="bi bi-eye"></i></a>
                            <form action="{{ route('articles.forceDestroy', $article) }}" method="POST" class="d-inline" onsubmit="return confirm('Suppression définitive ?')">
    @csrf @method('DELETE')
    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
</form>
                            <a href="{{ route('articles.edit', $article) }}" class="btn btn-outline-primary" title="Modifier"><i class="bi bi-pencil"></i></a>
                            <button class="btn btn-outline-warning" title="Ajuster stock" data-bs-toggle="modal" data-bs-target="#ajusterModal{{ $article->id }}">
                                <i class="bi bi-sliders"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- Modal ajustement -->
                <div class="modal fade" id="ajusterModal{{ $article->id }}" tabindex="-1">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header"><h6 class="modal-title">Ajuster stock — {{ $article->nom }}</h6></div>
                            <form action="{{ route('articles.ajuster', $article) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Quantité (négatif = retrait)</label>
                                        <input type="number" name="quantite" class="form-control" required placeholder="ex: 10 ou -5">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Motif</label>
                                        <input type="text" name="motif" class="form-control" required placeholder="Inventaire, correction...">
                                    </div>
                                    <div class="alert alert-info py-2 small mb-0">
                                        Stock actuel : <strong>{{ $article->quantite_stock }} {{ $article->unite }}</strong>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-sm btn-warning">Ajuster</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr><td colspan="8" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>Aucun article trouvé
                </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2">{{ $articles->links() }}</div>
    </div>
</div>
@endsection

