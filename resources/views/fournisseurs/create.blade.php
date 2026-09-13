@extends('layouts.app')
@section('title','Nouveau fournisseur')
@section('page-title','Nouveau fournisseur')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">
<div class="card-header bg-white py-3"><i class="bi bi-truck me-2 text-primary"></i>Informations du fournisseur</div>
<div class="card-body">
<form action="{{ route('fournisseurs.store') }}" method="POST">
@csrf
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Nom <span class="text-danger">*</span></label>
        <input type="text" name="nom" class="form-control" value="{{ old('nom') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Code</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $code) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Téléphone</label>
        <input type="text" name="telephone" class="form-control" value="{{ old('telephone') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Ville</label>
        <input type="text" name="ville" class="form-control" value="{{ old('ville') }}" placeholder="Ex: Ouagadougou">
    </div>
    <div class="col-md-6">
        <label class="form-label">Pays</label>
        <input type="text" name="pays" class="form-control" value="{{ old('pays', 'Burkina Faso') }}">
    </div>
    <div class="col-12">
        <label class="form-label">Adresse</label>
        <textarea name="adresse" class="form-control" rows="2">{{ old('adresse') }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
    </div>
</div>
<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
    <a href="{{ route('fournisseurs.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
</form>
</div>
</div>
</div>
</div>
@endsection