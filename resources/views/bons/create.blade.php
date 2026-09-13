@extends('layouts.app')
@section('title','Nouveau bon de commande')
@section('page-title','Nouveau bon de commande')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-10">

@if($articlesEnAlerte->count() > 0)
<div class="alert alert-warning d-flex align-items-start gap-2 mb-3">
    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
    <div>
        <strong>{{ $articlesEnAlerte->count() }} article(s) en alerte de stock :</strong>
        {{ $articlesEnAlerte->pluck('nom')->take(5)->implode(', ') }}{{ $articlesEnAlerte->count() > 5 ? '...' : '' }}
    </div>
</div>
@endif

<div class="card">
<div class="card-header bg-white py-3">
    <i class="bi bi-file-earmark-text me-2 text-primary"></i>Créer un bon de commande
</div>
<div class="card-body">
<form action="{{ route('bons.store') }}" method="POST" id="formBon">
@csrf
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label">N° Bon</label>
        <input type="text" class="form-control bg-light" value="{{ $numero }}" readonly>
    </div>
    <div class="col-md-3">
        <label class="form-label">Date commande <span class="text-danger">*</span></label>
        <input type="date" name="date_commande" class="form-control" value="{{ date('Y-m-d') }}" required>
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
        <label class="form-label">Livraison prévue</label>
        <input type="date" name="date_livraison_prevue" class="form-control">
    </div>
    <div class="col-md-3">
        <label class="form-label">Statut initial</label>
        <select name="statut" class="form-select">
            <option value="brouillon">Brouillon</option>
            <option value="envoyé">Envoyé au fournisseur</option>
        </select>
    </div>
</div>

<div class="card border mb-3">
    <div class="card-header bg-light py-2 fw-semibold">Articles à commander</div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:40%">Article</th>
                    <th style="width:15%">Stock actuel</th>
                    <th style="width:15%">Qté à commander</th>
                    <th style="width:20%">Prix unitaire (F)</th>
                    <th style="width:15%">Montant</th>
                    <th style="width:5%"></th>
                </tr>
            </thead>
            <tbody id="lignesArticles"></tbody>
            <tfoot>
                <tr class="table-light">
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
    <label class="form-label">Notes / Remarques</label>
    <textarea name="notes" class="form-control" rows="2" placeholder="Instructions spéciales pour le fournisseur..."></textarea>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>
        <i class="bi bi-check-lg me-1"></i>Créer le bon
    </button>
    <a href="{{ route('bons.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
</form>
</div>
</div>
</div>
</div>

<template id="templateLigne">
<tr class="ligne-article" data-index="__INDEX__">
    <td class="position-relative">
        <input type="text" class="form-control form-control-sm article-search" placeholder="Chercher article..." autocomplete="off">
        <input type="hidden" name="articles[__INDEX__][article_id]" class="article-id">
        <div class="autocomplete-results" style="display:none"></div>
    </td>
    <td><span class="stock-actuel text-muted small">-</span></td>
    <td><input type="number" name="articles[__INDEX__][quantite]" class="form-control form-control-sm quantite" min="1" value="1"></td>
    <td><input type="number" name="articles[__INDEX__][prix_unitaire]" class="form-control form-control-sm prix" min="0" step="1" value="0"></td>
    <td><span class="montant fw-semibold text-primary">0 F</span></td>
    <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="supprimerLigne(this)"><i class="bi bi-trash"></i></button></td>
</tr>
</template>

@push('scripts')
<script>
let indexLigne = 0;

function ajouterLigne() {
    const tpl = document.getElementById('templateLigne').innerHTML.replaceAll('__INDEX__', indexLigne++);
    document.getElementById('lignesArticles').insertAdjacentHTML('beforeend', tpl);
    const row = document.getElementById('lignesArticles').lastElementChild;
    initLigne(row);
    calculerTotal();
    document.getElementById('btnSubmit').disabled = false;
}

function supprimerLigne(btn) {
    btn.closest('tr').remove();
    calculerTotal();
    if (!document.querySelectorAll('.ligne-article').length)
        document.getElementById('btnSubmit').disabled = true;
}

function initLigne(row) {
    const searchInput = row.querySelector('.article-search');
    const articleId   = row.querySelector('.article-id');
    const stockActuel = row.querySelector('.stock-actuel');
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
                searchInput.value = art.nom + ' (' + art.reference + ')';
                articleId.value   = art.id;
                stockActuel.textContent = art.quantite_stock + ' ' + art.unite;
                stockActuel.className   = 'stock-actuel small ' + (art.quantite_stock > 5 ? 'text-success' : 'text-danger');
                prixInput.value   = art.prix_achat || 0;
                resultsDiv.style.display = 'none';
                calculerTotal();
            });
            resultsDiv.appendChild(item);
        });
        resultsDiv.style.display = 'block';
    });

    document.addEventListener('click', e => { if (!row.contains(e.target)) resultsDiv.style.display = 'none'; });
    row.querySelector('.quantite').addEventListener('input', calculerTotal);
    row.querySelector('.prix').addEventListener('input', calculerTotal);
}

function calculerTotal() {
    let total = 0;
    document.querySelectorAll('.ligne-article').forEach(row => {
        const q = parseFloat(row.querySelector('.quantite').value) || 0;
        const p = parseFloat(row.querySelector('.prix').value) || 0;
        const m = q * p; total += m;
        row.querySelector('.montant').textContent = m.toLocaleString('fr-FR') + ' F';
    });
    document.getElementById('totalGeneral').textContent = total.toLocaleString('fr-FR') + ' F';
}

ajouterLigne();
</script>
@endpush
@endsection
