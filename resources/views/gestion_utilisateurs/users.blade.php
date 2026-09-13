@extends('layouts.app')
@section('title','Utilisateurs')
@section('page-title','Gestion des utilisateurs')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <div></div>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i>Nouvel utilisateur
    </a>
</div>
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Rôle</th><th>Statut</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse($users as $user)
            <tr>
                <td class="fw-semibold">
                    {{ $user->name }}
                    @if($user->id === auth()->id()) <span class="badge bg-info text-white ms-1">Vous</span> @endif
                </td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->telephone ?? '-' }}</td>
                <td>
                    @if($user->role === 'admin') <span class="badge bg-danger text-white">Admin</span>
                    @elseif($user->role === 'gerant') <span class="badge bg-primary text-white">Gérant</span>
                    @else <span class="badge bg-secondary text-white">Vendeur</span>
                    @endif
                </td>
                <td>
                    @if($user->actif) <span class="badge badge-normal">Actif</span>
                    @else <span class="badge badge-rupture">Inactif</span> @endif
                </td>
                <td class="text-end">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-4 text-muted">Aucun utilisateur</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

