@extends('layouts.app')
@section('title','Sorties / Ventes')
@section('page-title','Sorties / Ventes')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <div></div>
    <a href="{{ route('sorties.create') }}" class="btn btn-danger">
        <i class="bi bi-plus-lg me-1"></i>Nouvelle sortie / Vente
    </a>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3"><input type="text" name="search" class="form-control" placeholder="N° ou client..." value="{{ request('search') }}"></div>
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">Tous types</option>
                    <option value="vente" {{ request('type') === 'vente' ? 'selected' : '' }}>Vente</option>
                    <option value="perte" {{ request('type') === 'perte' ? 'selected' : '' }}>Perte</option>
                    <option value="retour_fournisseur" {{ request('type') === 'retour_fournisseur' ? 'selected' : '' }}>Retour</option>
                </select>
            </div>
            <div class="col-md-2"><input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}"></div>
            <div class="col-md-2"><input type="date" name="date_fin" class="form-control" value="{{ request('date_fin') }}"></div>
            <div class="col-md-3 d-flex gap-1">
                <button class="btn btn-primary flex-fill">Filtrer</button>
                <a href="{{ route('sorties.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>N° Sortie</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Client</th>
                    <th>Montant</th>
                    <th>Remise</th>
                    <th>Statut</th>
                    <th>Par</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($sorties as $s)
            <tr>
                <td class="fw-semibold text-danger">{{ $s->numero }}</td>
                <td>{{ $s->date_sortie->format('d/m/Y') }}</td>
                <td><span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $s->type)) }}</span></td>
                <td>{{ $s->client_nom ?? '-' }}</td>
                <td class="fw-semibold">{{ number_format($s->montant_total, 0, ',', ' ') }} F</td>
                <td>{{ $s->remise > 0 ? number_format($s->remise, 0, ',', ' ') . ' F' : '-' }}</td>
                <td>
                    @if($s->statut === 'validé') <span class="badge badge-normal">Validé</span>
                    @elseif($s->statut === 'annulé') <span class="badge badge-rupture">Annulé</span>
                    @endif
                </td>
                <td class="small">{{ $s->user->name ?? '-' }}</td>
                <td class="text-end">
                    <a href="{{ route('sorties.show', $s) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>

                    {{-- Bouton Modifier – visible si le statut n'est pas "annulé" --}}
                    @if($s->statut !== 'annulé')
                        <a href="{{ route('sorties.edit', $s) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                    @endif

                    @if($s->statut === 'validé')
                        <form action="{{ route('sorties.annuler', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Annuler cette sortie ?')">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle"></i></button>
                        </form>
                    @endif

                    @if($s->statut === 'annulé')
                        <form action="{{ route('sorties.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer définitivement cette sortie ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Aucune sortie</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="px-3 py-2">{{ $sorties->links() }}</div>
    </div>
</div>
@endsection