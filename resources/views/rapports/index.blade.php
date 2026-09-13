@extends('layouts.app')
@section('title', 'Rapports')
@section('page-title', 'Rapports et analyses')

@section('content')
<div class="row g-3">
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('rapports.stock') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4 shadow-sm">
                <i class="bi bi-box-seam fs-1 text-primary"></i>
                <h5 class="mt-3">Stock actuel</h5>
                <p class="small text-muted">Voir l’inventaire complet et la valorisation</p>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('rapports.mouvements') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4 shadow-sm">
                <i class="bi bi-arrow-left-right fs-1 text-info"></i>
                <h5 class="mt-3">Mouvements de stock</h5>
                <p class="small text-muted">Historique entrées/sorties/ajustements</p>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('rapports.ventes') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4 shadow-sm">
                <i class="bi bi-graph-up fs-1 text-success"></i>
                <h5 class="mt-3">Ventes par période</h5>
                <p class="small text-muted">Chiffre d’affaires, top articles, panier moyen</p>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('rapports.entrees') }}" class="text-decoration-none">
            <div class="card h-100 text-center p-4 shadow-sm">
                <i class="bi bi-arrow-down-circle fs-1 text-warning"></i>
                <h5 class="mt-3">Entrées par période</h5>
                <p class="small text-muted">Approvisionnements, coûts, fournisseurs</p>
            </div>
        </a>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-white py-3">
        <i class="bi bi-calendar-week me-2"></i> Accès rapide
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <form action="{{ route('rapports.ventes') }}" method="GET" class="row g-2">
                    <div class="col-5">
                        <input type="date" name="date_debut" class="form-control" value="{{ now()->startOfMonth()->toDateString() }}">
                    </div>
                    <div class="col-5">
                        <input type="date" name="date_fin" class="form-control" value="{{ now()->toDateString() }}">
                    </div>
                    <div class="col-2">
                        <button class="btn btn-primary w-100">Voir ventes</button>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <form action="{{ route('rapports.entrees') }}" method="GET" class="row g-2">
                    <div class="col-5">
                        <input type="date" name="date_debut" class="form-control" value="{{ now()->startOfMonth()->toDateString() }}">
                    </div>
                    <div class="col-5">
                        <input type="date" name="date_fin" class="form-control" value="{{ now()->toDateString() }}">
                    </div>
                    <div class="col-2">
                        <button class="btn btn-primary w-100">Voir entrées</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection