@extends('layouts.app')
@section('title', 'Retour de prêt')
@section('page-title', 'Retour de prêt : ' . $pret->numero)

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
<div class="card-header bg-white py-3"><i class="bi bi-arrow-return-left me-2 text-success"></i>Confirmation de retour</div>
<div class="card-body">
<form action="{{ route('prets.retourner', $pret) }}" method="POST">
@csrf
<div class="mb-3">
    <label class="form-label">Date de retour réelle <span class="text-danger">*</span></label>
    <input type="date" name="date_retour_reelle" class="form-control" value="{{ date('Y-m-d') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Notes complémentaires</label>
    <textarea name="notes_retour" class="form-control" rows="2" placeholder="État de l'objet, observations..."></textarea>
</div>
<div class="alert alert-info">
    <strong>Prêt concerné :</strong><br>
    Article : {{ $pret->article->nom }} ({{ $pret->article->reference }})<br>
    Quantité prêtée : {{ $pret->quantite }} {{ $pret->article->unite }}<br>
    Emprunteur : {{ $pret->emprunteur_nom }}
</div>
<div class="d-flex gap-2">
    <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Confirmer le retour</button>
    <a href="{{ route('prets.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
</form>
</div>
</div>
</div>
</div>
@endsection
show.blade.php – Détail d’un prêt
blade
@extends('layouts.app')
@section('title', 'Prêt ' . $pret->numero)
@section('page-title', 'Prêt ' . $pret->numero)

@section('content')
<div class="row">
<div class="col-lg-6">
    <div class="card">
        <div class="card-header bg-white py-3">Informations</div>
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
                <tr><th>Statut</th><td><span class="badge bg-{{ $pret->statut === 'rendu' ? 'success' : ($pret->statut === 'en_cours' ? 'info' : 'secondary') }}">{{ ucfirst($pret->statut) }}</span></td></tr>
                <tr><th>Enregistré par</th><td>{{ $pret->user->name ?? '-' }}</td></tr>
            </table>
            </div>
            @if($pret->notes)
            <hr><strong>Notes :</strong> {{ $pret->notes }}
            @endif
        </div>
    </div>
</div>
<div class="col-lg-6">
    <div class="card">
        <div class="card-header bg-white py-3">Traçabilité</div>
        <div class="card-body">
            <p>Sortie associée : <code>{{ $pret->reference_sortie ?? '—' }}</code></p>
            <p>Consultez l'<a href="{{ route('articles.show', $pret->article) }}">historique des mouvements</a> de cet article pour voir l'impact du prêt et du retour.</p>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <a href="{{ route('prets.index') }}" class="btn btn-outline-secondary">Retour à la liste</a>
        @if($pret->statut === 'en_cours')
            <a href="{{ route('prets.retour', $pret) }}" class="btn btn-success">Enregistrer le retour</a>
        @endif
    </div>
</div>
</div>
@endsection