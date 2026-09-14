@extends('layouts.app')
@section('title','Entrées de stock')
@section('page-title','Entrées de stock')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <a href="{{ route('entrees.create') }}" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i>Nouvelle entrée
    </a>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3"><input type="text" name="search" class="form-control" placeholder="N° entrée..." value="{{ request('search') }}"></div>
            <div class="col-md-3">
                <select name="fournisseur" class="form-select">
                    <option value="">Tous fournisseurs</option>
                    @foreach($fournisseurs as $f)
                    <option value="{{ $f->id }}" {{ request('fournisseur') == $f->id ? 'selected' : '' }}>{{ $f->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}"></div>
            <div class="col-md-2"><input type="date" name="date_fin" class="form-control" value="{{ request('date_fin') }}"></div>
            <div class="col-md-2 d-flex gap-1">
                <button class="btn btn-primary flex-fill">Filtrer</button>
                <a href="{{ route('entrees.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                    <th>N° Entrée</th>
                    <th>Date</th>
                    <th>Fournisseur</th>
                    <th>Réf. bon</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Par</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($entrees as $e)
            <tr>
                <td class="fw-semibold text-success">{{ $e->numero }}</td>
                <td>{{ $e->date_entree->format('d/m/Y') }}</td>
                <td>{{ $e->fournisseur->nom ?? 'N/A' }}</td>
                <td>{{ $e->reference_bon ?? '-' }}</td>
                <td class="fw-semibold">{{ number_format($e->montant_total, 0, ',', ' ') }} F</td>
                <td>
                    @if($e->statut === 'validé') <span class="badge badge-normal">Validé</span>
                    @elseif($e->statut === 'annulé') <span class="badge badge-rupture">Annulé</span>
                    @else <span class="badge bg-secondary text-white">Brouillon</span>
                    @endif
                </td>
                <td class="small">{{ $e->user->name ?? '-' }}</td>
                <td class="text-end">
                    <a href="{{ route('entrees.show', $e) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>

                    {{-- Bouton Modifier – visible si le statut n'est pas "annulé" --}}
                    @if($e->statut !== 'annulé')
                        <a href="{{ route('entrees.edit', $e) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                    @endif

                    @if($e->statut === 'validé')
                        <form action="{{ route('entrees.annuler', $e) }}" method="POST" class="d-inline" onsubmit="return confirm('Annuler cette entrée ?')">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle"></i></button>
                        </form>
                    @endif

                    @if($e->statut === 'annulé')
                        <form action="{{ route('entrees.destroy', $e) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer définitivement cette entrée ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center py-4 text-muted"><i class="bi bi-inbox d-block fs-2 mb-2"></i>Aucune entrée</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
        <div class="px-3 py-2">{{ $entrees->links() }}</div>
    </div>
</div>
@endsection