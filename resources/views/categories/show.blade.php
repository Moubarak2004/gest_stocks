@extends('layouts.app')
@section('title', $categorie->nom)
@section('page-title', $categorie->nom)
@section('content')
<div class="card">
    <div class="card-body">
        <p><strong>Code :</strong> {{ $categorie->code ?? '-' }}</p>
        <p><strong>Description :</strong> {{ $categorie->description ?? '-' }}</p>
        <p><strong>Statut :</strong> {{ $categorie->actif ? 'Actif' : 'Inactif' }}</p>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Retour</a>
    </div>
</div>
@endsection