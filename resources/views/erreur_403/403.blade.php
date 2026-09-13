@extends('layouts.app')
@section('title','Accès refusé')
@section('page-title','Accès refusé')

@section('content')
<div class="text-center py-5">
    <i class="bi bi-shield-x text-danger" style="font-size:5rem"></i>
    <h3 class="mt-3 fw-bold">Accès refusé</h3>
    <p class="text-muted">Vous n'avez pas les droits nécessaires pour accéder à cette page.</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-2">
        <i class="bi bi-house me-1"></i>Retour au tableau de bord
    </a>
</div>
@endsection