@extends('layouts.app')
@section('title','Bons de commande')
@section('page-title','Bons de commande')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <div></div>
    <a href="{{ route('bons.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouveau bon
    </a>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="statut" class="form-select">
                    <option value="">Tous statuts</option>
                    @foreach(['brouillon','envoyé','reçu_partiel','reçu','annulé'] as $st)
                    <option value="{{ $st }}" {{ request('statut') === $st ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="fournisseur" class="form-select">
                    <option value="">Tous fournisseurs</option>
                    @foreach($fournisseurs as $f)
                    <option value="{{ $f->id }}" {{ request('fournisseur') == $f->id ? 'selected' : '' }}>{{ $f->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Filtrer</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>N° Bon</th>
                    <th>Date</th>
                    <th>Fournisseur</th>
                    <th>Livraison prévue</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($bons as $bon)
            <tr>
                <td class="fw-semibold text-primary">{{ $bon->numero }}</td>
                <td>{{ $bon->date_commande->format('d/m/Y') }}</td>
                <td>{{ $bon->fournisseur->nom ?? '-' }}</td>
                <td>{{ $bon->date_livraison_prevue ? $bon->date_livraison_prevue->format('d/m/Y') : '-' }}</td>
                <td>{{ number_format($bon->montant_total, 0, ',', ' ') }} F</td>
                <td>
                    @php $colors = ['brouillon'=>'bg-secondary text-white','envoyé'=>'bg-primary text-white','reçu_partiel'=>'badge-alerte','reçu'=>'badge-normal','annulé'=>'badge-rupture'] @endphp
                    <span class="badge {{ $colors[$bon->statut] ?? 'bg-secondary text-white' }}">{{ ucfirst(str_replace('_', ' ', $bon->statut)) }}</span>
                 </td>
                <td class="text-end">
                    <a href="{{ route('bons.show', $bon) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>

                    {{-- Bouton Modifier – visible uniquement si le bon est en brouillon --}}
                    @if($bon->statut === 'brouillon')
                        <a href="{{ route('bons.edit', $bon) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('bons.destroy', $bon) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce bon ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    @endif
                 </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Aucun bon</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="px-3 py-2">{{ $bons->links() }}</div>
    </div>
</div>
@endsection