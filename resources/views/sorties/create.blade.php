@extends('layouts.app')
@section('title','Nouvelle sortie/vente')
@section('page-title','Nouvelle sortie / Vente')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-10">
<div class="card">
<div class="card-header bg-white py-3">
    <i class="bi bi-arrow-up-circle me-2 text-danger"></i>Saisir une sortie de stock
</div>
<div class="card-body">
<form action="{{ route('sorties.store') }}" method="POST" id="formSortie">
@csrf
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label">N° Sortie</label>
        <input type="text" class="form-control bg-light" value="{{ $numero }}" readonly>
    </div>
    <div class="col-md-3">
        <label class="form-label">Date <span class="text-danger">*</span></label>
        <input type="date" name="date_sortie" class="form-control" value="{{ date('Y-m-d') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Type <span class="text-danger">*</span></label>
        <select name="type" class="form-select" required>
            <option value="vente">Vente</option>
            <option value="retour_fournisseur">Retour fournisseur</option>
            <option value="perte">Perte / Casse</option>
            <option value="transfert">Transfert</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Nom client</label>
        <input type="text" name="client_nom" class="form-control" placeholder="Optionnel">
    </div>
    <div class="col-md-3">
        <label class="form-label">Téléphone client</label>
        <input type="text" name="client_telephone" class="form-control" placeholder="Optionnel">
    </div>
</div>

<!-- Lignes articles -->
<div class="card border mb-3">
    <div class="card-header bg-light py-2 fw-semibold">Articles</div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:38%">Article</th>
                    <th style="width:12%">Stock dispo.</th>
                    <th style="width:12%">Quantité</th>
                    <th style="width:18%">Prix unitaire (F)</th>
                    <th style="width:15%">Montant</th>
                    <th style="width:5%"></th>
                </tr>
            </thead>
            <tbody id="lignesArticles"></tbody>
            <tfoot>
                <tr class="table-light">
                    <td colspan="4" class="text-end fw-bold">Sous-total</td>
                    <td class="fw-bold" id="sousTotal">0 F</td><td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer bg-white">
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="ajouterLigne()">
            <i class="bi bi-plus-lg me-1"></i>Ajouter un article
        </button>
    </div>
</div>

<!-- Remise et paiement -->
<div class="row g-3 justify-content-end">
    <div class="col-md-4">
        <div class="card border">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted small">Sous-total :</span>
                    <span id="affSousTotal" class="fw-semibold">0 F</span>
                </div>
                <div class="mb-2">
                    <label class="form-label small mb-1">Remise (F)</label>
                    <input type="number" name="remise" id="remiseInput" class="form-control form-control-sm" min="0" value="0">
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold">Total à payer :</span>
                    <span id="totalPayer" class="fw-bold text-primary fs-6">0 F</span>
                </div>
                <div class="mb-2">
                    <label class="form-label small mb-1">Montant reçu (F)</label>
                    <input type="number" name="montant_recu" id="montantRecu" class="form-control form-control-sm" min="0" value="0">
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-success small">Monnaie :</span>
                    <span id="monnaie" class="fw-semibold text-success">0 F</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <label class="form-label">Notes</label>
    <textarea name="notes" class="form-control" rows="2"></textarea>
</div>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-danger" id="btnSubmit" disabled>
        <i class="bi bi-check-lg me-1"></i>Valider la sortie
    </button>
    <a href="{{ route('sorties.index') }}" class="btn btn-outline-secondary">Annuler</a>
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
    <td><span class="stock-dispo text-muted small">-</span></td>
    <td><input type="number" name="articles[__INDEX__][quantite]" class="form-control form-control-sm quantite" min="1" value="1"></td>
    <td><input type="number" name="articles[__INDEX__][prix_unitaire]" class="form-control form-control-sm prix" min="0" step="1" value="0"></td>
    <td><span class="montant fw-semibold text-danger">0 F</span></td>
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
                searchInput.value = art.nom + ' (' + art.reference + ')';
                articleId.value   = art.id;
                stockDispo.textContent = art.quantite_stock + ' ' + art.unite;
                stockDispo.className   = 'stock-dispo small ' + (art.quantite_stock > 5 ? 'text-success' : 'text-danger');
                prixInput.value   = art.prix_vente || 0;
                row.querySelector('.quantite').max = art.quantite_stock;
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
    let sousTot = 0;
    document.querySelectorAll('.ligne-article').forEach(row => {
        const q = parseFloat(row.querySelector('.quantite').value) || 0;
        const p = parseFloat(row.querySelector('.prix').value) || 0;
        const m = q * p; sousTot += m;
        row.querySelector('.montant').textContent = m.toLocaleString('fr-FR') + ' F';
    });
    const remise = parseFloat(document.getElementById('remiseInput').value) || 0;
    const total  = Math.max(0, sousTot - remise);
    document.getElementById('sousTotal').textContent   = sousTot.toLocaleString('fr-FR') + ' F';
    document.getElementById('affSousTotal').textContent = sousTot.toLocaleString('fr-FR') + ' F';
    document.getElementById('totalPayer').textContent  = total.toLocaleString('fr-FR') + ' F';
    const recu   = parseFloat(document.getElementById('montantRecu').value) || 0;
    document.getElementById('monnaie').textContent = Math.max(0, recu - total).toLocaleString('fr-FR') + ' F';
}

document.getElementById('remiseInput').addEventListener('input', calculerTotal);
document.getElementById('montantRecu').addEventListener('input', calculerTotal);
ajouterLigne();
</script>
@endpush
@endsection
