@extends('layouts.app')
@section('title','Modifier article')
@section('page-title','Modifier article')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9">
<div class="card">
<div class="card-header bg-white py-3"><i class="bi bi-pencil me-2 text-primary"></i>Modifier — {{ $article->nom }}</div>
<div class="card-body">
<form action="{{ route('articles.update', $article) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Nom <span class="text-danger">*</span></label>
        <input type="text" name="nom" class="form-control" value="{{ old('nom', $article->nom) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Référence <span class="text-danger">*</span></label>
        <input type="text" name="reference" class="form-control" value="{{ old('reference', $article->reference) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Code barre</label>
        <input type="text" name="code_barre" class="form-control" value="{{ old('code_barre', $article->code_barre) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Catégorie</label>
        <select name="categorie_id" class="form-select">
            <option value="">-- Aucune --</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('categorie_id', $article->categorie_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nom }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Fournisseur</label>
        <select name="fournisseur_id" class="form-select">
            <option value="">-- Aucun --</option>
            @foreach($fournisseurs as $f)
            <option value="{{ $f->id }}" {{ old('fournisseur_id', $article->fournisseur_id) == $f->id ? 'selected' : '' }}>{{ $f->nom }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="2">{{ old('description', $article->description) }}</textarea>
    </div>
    <div class="col-md-3">
        <label class="form-label">Prix achat (F) <span class="text-danger">*</span></label>
        <input type="number" name="prix_achat" class="form-control" step="1" min="0" value="{{ old('prix_achat', $article->prix_achat) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Prix vente gros (F) <span class="text-danger">*</span></label>
        <input type="number" name="prix_vente" class="form-control" step="1" min="0" value="{{ old('prix_vente', $article->prix_vente) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Prix vente détail (F)</label>
        <input type="number" name="prix_vente_detail" class="form-control" step="1" min="0" value="{{ old('prix_vente_detail', $article->prix_vente_detail) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Unité</label>
        <select name="unite" class="form-select">
            @foreach(['Pièce','Kg','Litre','Carton','Sachet','Boîte','Mètre','Rouleau','Paquet'] as $u)
            <option {{ old('unite', $article->unite) === $u ? 'selected' : '' }}>{{ $u }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Seuil d'alerte <span class="text-danger">*</span></label>
        <input type="number" name="stock_minimum" class="form-control" min="0" value="{{ old('stock_minimum', $article->stock_minimum) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Stock maximum</label>
        <input type="number" name="stock_maximum" class="form-control" min="0" value="{{ old('stock_maximum', $article->stock_maximum) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Emplacement</label>
        <input type="text" name="emplacement" class="form-control" value="{{ old('emplacement', $article->emplacement) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Actif</label>
        <select name="actif" class="form-select">
            <option value="1" {{ $article->actif ? 'selected' : '' }}>Oui</option>
            <option value="0" {{ !$article->actif ? 'selected' : '' }}>Non</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Nouvelle photo</label>
        <input type="file" name="image" class="form-control" accept="image/*">
        @if($article->image)
        <small class="text-muted">Photo actuelle : {{ basename($article->image) }}</small>
        @endif
    </div>
    <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $article->notes) }}</textarea>
    </div>
</div>
<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
    <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
</form>
</div>
</div>
</div>
</div>
@endsection

