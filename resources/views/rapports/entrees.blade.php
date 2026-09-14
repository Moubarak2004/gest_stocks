@extends('layouts.app')
@section('title','Rapport Entrées')
@section('page-title','Rapport des Entrées')

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
            <div class="fw-bold fs-5 text-primary">{{ number_format($totalEntrees, 0, ',', ' ') }} F</div>
            <div class="text-muted small">Total des approvisionnements</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <div class="fw-bold fs-4 text-info">{{ $entrees->count() }}</div>
            <div class="text-muted small">Entrées validées</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <div class="fw-bold fs-5 text-success">{{ $entrees->count() > 0 ? number_format($totalEntrees / $entrees->count(), 0, ',', ' ') : 0 }} F</div>
            <div class="text-muted small">Montant moyen / entrée</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3 fw-semibold"><i class="bi bi-list-ul me-2"></i>Détail des entrées</div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>N° Entrée</th><th>Date</th><th>Fournisseur</th><th>Réf. bon</th><th>Montant</th><th>Par</th></tr></thead>
            <tbody>
            @forelse($entrees as $e)
            <tr>
                <td class="fw-semibold text-success small">
                    <a href="{{ route('entrees.show', $e) }}" class="text-success">{{ $e->numero }}</a>
                </td>
                <td class="small">{{ $e->date_entree->format('d/m/Y') }}</td>
                <td class="small">{{ $e->fournisseur->nom ?? 'N/A' }}</td>
                <td class="small text-muted">{{ $e->reference_bon ?? '-' }}</td>
                <td class="fw-bold text-primary">{{ number_format($e->montant_total, 0, ',', ' ') }} F</td>
                <td class="small">{{ $e->user->name ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-4 text-muted">Aucune entrée sur cette période</td></tr>
            @endforelse
            </tbody>
            @if($entrees->count())
            <tfoot class="table-light">
                <tr>
                    <td colspan="4" class="text-end fw-bold">TOTAL</td>
                    <td class="fw-bold text-primary">{{ number_format($totalEntrees, 0, ',', ' ') }} F</td>
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