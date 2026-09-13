@extends('layouts.app')
@section('title', $fournisseur->nom)
@section('page-title', $fournisseur->nom)

@section('content')
<div class="row g-3">
<div class="col-lg-4">
    <div class="card mb-3">
        <div class="card-header bg-white py-3 fw-semibold"><i class="bi bi-info-circle me-2"></i>Informations</div>
        <div class="card-body">
            <table class="table table-sm mb-0">
                <tr><td class="text-muted">Code</td><td>{{ $fournisseur->code ?? '-' }}</td></tr>
                <tr><td class="text-muted">Email</td><td>{{ $fournisseur->email ?? '-' }}</td></tr>
                <tr><td class="text-muted">Téléphone</td><td>{{ $fournisseur->telephone ?? '-' }}</td></tr>
                <tr><td class="text-muted">Adresse</td><td>{{ $fournisseur->adresse ?? '-' }}</td></tr>
                <tr><td class="text-muted">Ville</td><td>{{ $fournisseur->ville ?? '-' }}</td></tr>
                <tr><td class="text-muted">Pays</td><td>{{ $fournisseur->pays }}</td></tr>
                <tr><td class="text-muted">Statut</td>
                    <td>@if($fournisseur->actif)<span class="badge badge-normal">Actif</span>
                        @else<span class="badge badge-rupture">Inactif</span>@endif</td></tr>
            </table>
        </div>
    </div>
    @if($fournisseur->notes)
    <div class="card mb-3">
        <div class="card-body small"><strong>Notes :</strong> {{ $fournisseur->notes }}</div>
    </div>
    @endif
    <div class="d-flex gap-2">
        <a href="{{ route('fournisseurs.edit', $fournisseur) }}" class="btn btn-primary flex-fill">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        <a href="{{ route('fournisseurs.index') }}" class="btn btn-outline-secondary">Retour</a>
    </div>
</div>

<div class="col-lg-8">
    <div class="card mb-3">
        <div class="card-header bg-white py-3 fw-semibold">
            <i class="bi bi-box2 me-2 text-primary"></i>Articles associés ({{ $fournisseur->articles->count() }})
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead><tr><th>Article</th><th>Référence</th><th>Stock</th><th>Prix achat</th></tr></thead>
                <tbody>
                @forelse($fournisseur->articles as $art)
                <tr>
                    <td class="fw-semibold small">{{ $art->nom }}</td>
                    <td class="text-muted small">{{ $art->reference }}</td>
                    <td class="{{ $art->quantite_stock <= 0 ? 'text-danger' : 'text-success' }} fw-semibold">
                        {{ $art->quantite_stock }} {{ $art->unite }}
                    </td>
                    <td>{{ number_format($art->prix_achat, 0, ',', ' ') }} F</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-2">Aucun article</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white py-3 fw-semibold">
            <i class="bi bi-arrow-down-circle me-2 text-success"></i>Dernières entrées
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead><tr><th>N°</th><th>Date</th><th>Montant</th><th>Statut</th><th>Par</th></tr></thead>
                <tbody>
                @forelse($entrees as $e)
                <tr>
                    <td class="fw-semibold text-success small">
                        <a href="{{ route('entrees.show', $e) }}" class="text-success">{{ $e->numero }}</a>
                    </td>
                    <td>{{ $e->date_entree->format('d/m/Y') }}</td>
                    <td>{{ number_format($e->montant_total, 0, ',', ' ') }} F</td>
                    <td><span class="badge {{ $e->statut === 'validé' ? 'badge-normal' : 'badge-rupture' }}">{{ $e->statut }}</span></td>
                    <td class="small">{{ $e->user->name ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-2">Aucune entrée</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="px-3 py-2">{{ $entrees->links() }}</div>
        </div>
    </div>
</div>
</div>
@endsection

