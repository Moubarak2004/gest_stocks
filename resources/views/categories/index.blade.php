@extends('layouts.app')
@section('title', 'Catégories')
@section('page-title', 'Catégories')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <form method="GET" action="{{ route('categories.index') }}" class="d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Nom, code, description..." value="{{ request('search') }}" style="width: 250px">
        <button class="btn btn-primary">Rechercher</button>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Reset</a>
    </form>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouvelle catégorie
    </a>
</div>

@if(request('search'))
    <div class="mb-2 text-muted">
        Résultats pour "<strong>{{ request('search') }}</strong>" : {{ $categories->total() }} catégorie(s) trouvée(s)
    </div>
@endif

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Nom</th>
                    <th>Articles</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td class="small text-muted">{{ $cat->code ?? '-' }}</td>
                    <td class="fw-semibold">{{ $cat->nom }}</td>
                    <td><span class="badge bg-primary text-white">{{ $cat->articles_count }}</span></td>
                    <td>
                        @if($cat->actif)
                            <span class="badge badge-normal">Actif</span>
                        @else
                            <span class="badge badge-rupture">Inactif</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('categories.show', $cat) }}" class="btn btn-outline-info" title="Voir">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('categories.edit', $cat) }}" class="btn btn-outline-primary" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette catégorie ?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger" title="Supprimer" {{ $cat->articles_count > 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>Aucune catégorie
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-3 py-2">
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection