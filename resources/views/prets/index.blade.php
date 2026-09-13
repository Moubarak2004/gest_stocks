@extends('layouts.app')
@section('title', 'Prêts d\'objets')
@section('page-title', 'Prêts d\'objets')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <div></div>
    <a href="{{ route('prets.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouveau prêt
    </a>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="statut" class="form-select">
                    <option value="">Tous statuts</option>
                    <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="retard"   {{ request('statut') == 'retard' ? 'selected' : '' }}>En retard</option>
                    <option value="rendu"    {{ request('statut') == 'rendu' ? 'selected' : '' }}>Rendu</option>
                    <option value="annule"   {{ request('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            <div class="col-md-5">
                <input type="text" name="recherche" class="form-control" placeholder="N° prêt, emprunteur, article..." value="{{ request('recherche') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Filtrer</button>
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
                        <th>N° Prêt</th>
                        <th>Article</th>
                        <th>Qté</th>
                        <th>Emprunteur</th>
                        <th>Date prêt</th>
                        <th>Retour prévu</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($prets as $pret)
                    <tr>
                        <td class="fw-semibold text-primary">{{ $pret->numero }}</td>
                        <td>{{ $pret->article->nom ?? '-' }}<br><small class="text-muted">{{ $pret->article->reference ?? '' }}</small></td>
                        <td>{{ $pret->quantite }} {{ $pret->article->unite ?? '' }}</td>
                        <td>{{ $pret->emprunteur_nom }}</td>
                        <td>{{ $pret->date_pret->format('d/m/Y') }}</td>
                        <td>{{ $pret->date_retour_prevue ? $pret->date_retour_prevue->format('d/m/Y') : '-' }}</td>
                        <td>
                            @php
                                $badges = ['en_cours' => 'bg-info text-white', 'retard' => 'bg-warning text-dark', 'rendu' => 'bg-success text-white', 'annule' => 'bg-secondary text-white'];
                            @endphp
                            <span class="badge {{ $badges[$pret->statut] ?? 'bg-secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $pret->statut)) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('prets.show', $pret) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>

                            {{-- Bouton Modifier – visible uniquement si le prêt est en cours --}}
                            @if($pret->statut === 'en_cours')
                                <a href="{{ route('prets.edit', $pret) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                <a href="{{ route('prets.retour', $pret) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-arrow-return-left"></i></a>
                                <form action="{{ route('prets.annuler', $pret) }}" method="POST" class="d-inline" onsubmit="return confirm('Annuler ce prêt ? Le stock sera réintégré.')">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle"></i></button>
                                </form>
                            @endif
                         </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">Aucun prêt trouvé.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2">{{ $prets->links() }}</div>
    </div>
</div>
@endsection