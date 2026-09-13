@extends('layouts.app')
@section('title','Nouvelle entrée')
@section('page-title','Nouvelle entrée de stock')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-10">
<div class="card">
<div class="card-header bg-white py-3">
    <i class="bi bi-arrow-down-circle me-2 text-success"></i>Saisir une entrée de stock
</div>
<div class="card-body">
<form action="{{ route('entrees.store') }}" method="POST" id="formEntree">
@csrf
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label">N° Entrée</label>
        <input type="text" class="form-control bg-light" value="{{ $numero }}" readonly>
    </div>
    <div class="col-md-3">
        <label class="form-label">Date <span class="text-danger">*</span></label>
        <input type="date" name="date_entree" class="form-control" value="{{ date('Y-m-d') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Fournisseur</label>
        <select name="fournisseur_id" class="form-select">
            <option value="">-- Sélectionner --</option>
            @foreach($fournisseurs as $f)
            <option value="{{ $f->id }}">{{ $f->nom }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Réf. bon livraison</label>
        <input type="text" name="reference_bon" class="form-control" placeholder="Optionnel">
    </div>
</div>

<!-- Lignes articles -->
<div class="card border mb-3">
    <div class="card-header bg-light py-2 fw-semibold">Articles</div>
    <div class="card-body p-0">
        <table class="table mb-0" id="tableArticles">
            <thead class="table-light">
                <tr>
                    <th style="width:40%">Article</th>
                    <th style="width:15%">Stock dispo.</th>
                    <th style="width:15%">Quantité</th>
                    <th style="width:20%">Prix unitaire (F)</th>
                    <th style="width:15%">Montant</th>
                    <th style="width:5%"></th>
                </tr>
            </thead>
            <tbody id="lignesArticles">
                <!-- Lignes dynamiques -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end fw-bold">TOTAL</td>
                    <td class="fw-bold text-primary fs-6" id="totalGeneral">0 F</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer bg-white">
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="ajouterLigne()">
            <i class="bi bi-plus-lg me-1"></i>Ajouter un article
        </button>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Notes</label>
    <textarea name="notes" class="form-control" rows="2"></textarea>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-success" id="btnSubmit" disabled>
        <i class="bi bi-check-lg me-1"></i>Valider l'entrée
    </button>
    <a href="{{ route('entrees.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
</form>
</div>
</div>
</div>
</div>

<!-- Template ligne (hidden) -->
<template id="templateLigne">
<tr class="ligne-article" data-index="__INDEX__">
    <td class="position-relative">
        <input type="text" class="form-control article-search" placeholder="Chercher article..." autocomplete="off">
        <input type="hidden" name="articles[__INDEX__][article_id]" class="article-id">
        <div class="autocomplete-results" style="display:none"></div>
    </td>
    <td><span class="stock-dispo text-muted small">-</span></td>
    <td><input type="number" name="articles[__INDEX__][quantite]" class="form-control quantite" min="1" value="1"></td>
    <td><input type="number" name="articles[__INDEX__][prix_unitaire]" class="form-control prix" min="0" step="1" value="0"></td>
    <td><span class="montant fw-semibold text-primary">0 F</span></td>
    <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="supprimerLigne(this)"><i class="bi bi-trash"></i></button></td>
</tr>
</template>

@push('scripts')
<script>
let indexLigne = 0;

function ajouterLigne() {
    const tpl = document.getElementById('templateLigne').innerHTML.replaceAll('__INDEX__', indexLigne++);
    const tbody = document.getElementById('lignesArticles');
    tbody.insertAdjacentHTML('beforeend', tpl);
    const row = tbody.lastElementChild;
    initLigne(row);
    calculerTotal();
    document.getElementById('btnSubmit').disabled = false;
}

function supprimerLigne(btn) {
    btn.closest('tr').remove();
    calculerTotal();
    if (document.querySelectorAll('.ligne-article').length === 0) {
        document.getElementById('btnSubmit').disabled = true;
    }
}

function initLigne(row) {
    const searchInput = row.querySelector('.article-search');
    const articleId   = row.querySelector('.article-id');
    const stockDispo  = row.querySelector('.stock-dispo');
    const prixInput   = row.querySelector('.prix');
    const resultsDiv  = row.querySelector('.autocomplete-results');

    searchInput.addEventListener('input', async function() {
        const q = this.value.trim();
        if (q.length < 2) { resultsDiv.style.display = 'none'; return; }
        const res  = await fetch(`/articles/search?q=${encodeURIComponent(q)}`);
        const data = await res.json();
        resultsDiv.innerHTML = '';
        if (!data.length) { resultsDiv.style.display = 'none'; return; }
        data.forEach(art => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item';
            item.innerHTML = `<span>${art.nom} <small class="text-muted">(${art.reference})</small></span>
                              <span class="${art.quantite_stock > 5 ? 'text-success' : 'text-danger'}">${art.quantite_stock} ${art.unite}</span>`;
            item.addEventListener('click', () => {
                searchInput.value   = art.nom + ' (' + art.reference + ')';
                articleId.value     = art.id;
                stockDispo.textContent = art.quantite_stock + ' ' + art.unite;
                prixInput.value     = art.prix_achat || 0;
                resultsDiv.style.display = 'none';
                calculerTotal();
            });
            resultsDiv.appendChild(item);
        });
        resultsDiv.style.display = 'block';
    });

    document.addEventListener('click', e => {
        if (!row.contains(e.target)) resultsDiv.style.display = 'none';
    });

    row.querySelector('.quantite').addEventListener('input', calculerTotal);
    row.querySelector('.prix').addEventListener('input', calculerTotal);
}

function calculerTotal() {
    let total = 0;
    document.querySelectorAll('.ligne-article').forEach(row => {
        const q = parseFloat(row.querySelector('.quantite').value) || 0;
        const p = parseFloat(row.querySelector('.prix').value) || 0;
        const m = q * p;
        row.querySelector('.montant').textContent = m.toLocaleString('fr-FR') + ' F';
        total += m;
    });
    document.getElementById('totalGeneral').textContent = total.toLocaleString('fr-FR') + ' F';
}

// Ajouter une première ligne au chargement
ajouterLigne();
</script>
@endpush
@endsection
