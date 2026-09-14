@extends('layouts.app')
@section('title', 'Prêt ' . $pret->numero)
@section('page-title', 'Prêt ' . $pret->numero)

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white py-3">
                <i class="bi bi-info-circle me-2 text-primary"></i> Informations
            </div>
            <div class="card-body">
                <div class="table-responsive">
                <table class="table table-sm">
                    <tr><th>N° prêt</th><td>{{ $pret->numero }}</td></tr>
                    <tr><th>Article</th><td>{{ $pret->article->nom }} ({{ $pret->article->reference }})</td></tr>
                    <tr><th>Quantité</th><td>{{ $pret->quantite }} {{ $pret->article->unite }}</td></tr>
                    <tr><th>Emprunteur</th><td>{{ $pret->emprunteur_nom }} @if($pret->emprunteur_telephone) ({{ $pret->emprunteur_telephone }}) @endif</td></tr>
                    <tr><th>Date du prêt</th><td>{{ $pret->date_pret->format('d/m/Y') }}</td></tr>
                    <tr><th>Retour prévu</th><td>{{ $pret->date_retour_prevue ? $pret->date_retour_prevue->format('d/m/Y') : '-' }}</td></tr>
                    <tr><th>Retour réel</th><td>{{ $pret->date_retour_reelle ? $pret->date_retour_reelle->format('d/m/Y') : '-' }}</td></tr>
                    <tr><th>Statut</th>
                        <td>
                            @php
                                $badges = ['en_cours' => 'bg-info text-white', 'retard' => 'bg-warning text-dark', 'rendu' => 'bg-success text-white', 'annule' => 'bg-secondary text-white'];
                            @endphp
                            <span class="badge {{ $badges[$pret->statut] ?? 'bg-secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $pret->statut)) }}
                            </span>
                        </td>
                    </tr>
                    <tr><th>Enregistré par</th><td>{{ $pret->user->name ?? '-' }}</td></tr>
                </table>
                </div>
                @if($pret->notes)
                    <hr>
                    <strong>Notes :</strong> {{ nl2br(e($pret->notes)) }}
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white py-3">
                <i class="bi bi-link-45deg me-2 text-info"></i> Traçabilité
            </div>
            <div class="card-body">
                <p>Sortie associée : <code>{{ $pret->reference_sortie ?? '—' }}</code></p>
                <p>
                    <a href="{{ route('articles.show', $pret->article) }}">
                        Voir l’historique des mouvements de cet article
                    </a>
                </p>
            </div>
        </div>
        <div class="mt-3 d-flex gap-2">
            <a href="{{ route('prets.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Retour à la liste
            </a>
            @if($pret->statut === 'en_cours')
                <a href="{{ route('prets.retour', $pret) }}" class="btn btn-success">
                    <i class="bi bi-arrow-return-left me-1"></i> Enregistrer le retour
                </a>
            @endif
        </div>
    </div>
</div>
<a href="{{ route('prets.pdf', $pret) }}" class="btn btn-sm btn-primary">
    <i class="bi bi-printer me-1"></i> PDF
</a>
@endsection