@extends('layouts.app')
@section('title','Mouvements de Stock')
@section('page-title','Mouvements de Stock')

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
            <div class="col-md-2">
                <label class="form-label small mb-1">&nbsp;</label>
                <a href="{{ route('rapports.mouvements') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3 fw-semibold">
        <i class="bi bi-arrow-left-right me-2"></i>Historique complet des mouvements
        <span class="badge bg-secondary ms-2">{{ $mouvements->total() }} mouvements</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Date/Heure</th><th>Article</th><th>Type</th><th>Quantité</th><th>Avant</th><th>Après</th><th>Référence</th><th>Motif</th><th>Par</th></tr>
                </thead>
                <tbody>
                @forelse($mouvements as $mvt)
                <tr>
                    <td class="small text-muted">{{ $mvt->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="fw-semibold small">{{ $mvt->article->nom ?? '-' }}</div>
                        <div class="text-muted" style="font-size:0.7rem">{{ $mvt->article->reference ?? '' }}</div>
                    </td>
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
                    <td class="text-muted small">{{ $mvt->stock_avant }}</td>
                    <td class="fw-semibold small">{{ $mvt->stock_apres }}</td>
                    <td class="small text-muted">{{ $mvt->reference_document ?? '-' }}</td>
                    <td class="small">{{ $mvt->motif ?? '-' }}</td>
                    <td class="small">{{ $mvt->user->name ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4 text-muted">Aucun mouvement</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2">{{ $mouvements->links() }}</div>
    </div>
</div>
<div class="mt-3">
    <a href="{{ route('rapports.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour aux rapports
    </a>
</div>
@endsection
