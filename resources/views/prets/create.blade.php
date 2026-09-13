@extends('layouts.app')
@section('title', 'Nouveau prêt')
@section('page-title', 'Nouveau prêt d\'objet')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">
<div class="card-header bg-white py-3"><i class="bi bi-hand-thumbs-up me-2 text-primary"></i>Enregistrer un prêt</div>
<div class="card-body">
<form action="{{ route('prets.store') }}" method="POST">
@csrf
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">N° prêt</label>
        <input type="text" class="form-control bg-light" value="{{ $numero }}" readonly>
    </div>
    <div class="col-md-4">
        <label class="form-label">Date du prêt <span class="text-danger">*</span></label>
        <input type="date" name="date_pret" class="form-control" value="{{ date('Y-m-d') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Retour prévu</label>
        <input type="date" name="date_retour_prevue" class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label">Article <span class="text-danger">*</span></label>
        <select name="article_id" class="form-select" required>
            <option value="">-- Choisir --</option>
            @foreach($articles as $art)
            <option value="{{ $art->id }}" data-stock="{{ $art->quantite_stock }}">
                {{ $art->nom }} ({{ $art->reference }}) – Stock: {{ $art->quantite_stock }} {{ $art->unite }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Quantité <span class="text-danger">*</span></label>
        <input type="number" name="quantite" class="form-control" min="1" value="1" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Unité</label>
        <input type="text" class="form-control" id="unite_aff" readonly>
    </div>

    <div class="col-md-6">
        <label class="form-label">Emprunteur (nom) <span class="text-danger">*</span></label>
        <input type="text" name="emprunteur_nom" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Téléphone</label>
        <input type="text" name="emprunteur_telephone" class="form-control">
    </div>

    <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="2"></textarea>
    </div>
</div>
<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer le prêt</button>
    <a href="{{ route('prets.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
</form>
</div>
</div>
</div>
</div>

@push('scripts')
<script>
    // Afficher l'unité quand on sélectionne un article
    const selectArt = document.querySelector('select[name="article_id"]');
    const uniteField = document.getElementById('unite_aff');
    selectArt.addEventListener('change', function() {
        const selected = selectArt.options[selectArt.selectedIndex];
        const text = selected.textContent;
        const match = text.match(/\(([^)]+)\)/);
        if (match) {
            uniteField.value = match[1]; // affiche l'unité approximative (à améliorer)
        } else {
            uniteField.value = '';
        }
    });
</script>
@endpush
@endsection
