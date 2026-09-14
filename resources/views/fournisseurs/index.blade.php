@extends('layouts.app')
@section('title','Fournisseurs')
@section('page-title','Fournisseurs')

@section('content')
<div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
    <form method="GET" class="d-flex flex-wrap gap-2">
        <input type="text" name="search" class="form-control" placeholder="Nom, téléphone..." value="{{ request('search') }}" style="min-width: 180px; flex: 1 1 220px;">
        <button class="btn btn-primary">Rechercher</button>
        <a href="{{ route('fournisseurs.index') }}" class="btn btn-outline-secondary">Reset</a>
    </form>
    <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouveau fournisseur
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Code</th><th>Nom</th><th>Téléphone</th><th>Ville</th><th>Articles</th><th>Statut</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            @forelse($fournisseurs as $f)
            <tr>
                <td class="small text-muted">{{ $f->code ?? '-' }}</td>
                <td class="fw-semibold">{{ $f->nom }}</td>
                <td>{{ $f->telephone ?? '-' }}</td>
                <td>{{ $f->ville ?? '-' }}</td>
                <td><span class="badge bg-primary text-white">{{ $f->articles_count }}</span></td>
                <td>
                    @if($f->actif) <span class="badge badge-normal">Actif</span>
                    @else <span class="badge badge-rupture">Inactif</span> @endif
                </td>
                <td class="text-end">
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('fournisseurs.show', $f) }}" class="btn btn-outline-info"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('fournisseurs.edit', $f) }}" class="btn btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <!-- Bouton supprimer toujours présent (contrôleur vérifiera si suppression autorisée) -->
                        <form action="{{ route('fournisseurs.destroy', $f) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce fournisseur ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Aucun fournisseur</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
        <div class="px-3 py-2">{{ $fournisseurs->links() }}</div>
    </div>
</div>
@endsection