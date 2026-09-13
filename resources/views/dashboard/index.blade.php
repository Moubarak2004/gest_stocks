@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#2563eb,#3b82f6)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ number_format($totalArticles) }}</div>
                    <div class="stat-label">Articles actifs</div>
                </div>
                <i class="bi bi-box2"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#f59e0b,#fbbf24)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ number_format($articlesAlerte) }}</div>
                    <div class="stat-label">En alerte</div>
                </div>
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#ef4444,#f87171)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ number_format($articlesRupture) }}</div>
                    <div class="stat-label">En rupture</div>
                </div>
                <i class="bi bi-x-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#10b981,#34d399)">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value" style="font-size:1.2rem">{{ number_format($valeurStock, 0, ',', ' ') }} F</div>
                    <div class="stat-label">Valeur du stock</div>
                </div>
                <i class="bi bi-currency-exchange"></i>
            </div>
        </div>
    </div>
</div>

<!-- Deuxième ligne -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card p-3">
            <div class="text-muted small">Entrées ce mois</div>
            <div class="fw-bold fs-5 text-primary">{{ number_format($entreesduMois, 0, ',', ' ') }} F</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3">
            <div class="text-muted small">Sorties ce mois</div>
            <div class="fw-bold fs-5 text-success">{{ number_format($sortiesDuMois, 0, ',', ' ') }} F</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3">
            <div class="text-muted small">Alertes non lues</div>
            <div class="fw-bold fs-5 text-danger">{{ $alertesNonLues }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3">
            <div class="text-muted small">Bons en attente</div>
            <div class="fw-bold fs-5 text-warning">{{ $bonsEnAttente }}</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Graphique -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bar-chart me-2 text-primary"></i>Entrées / Sorties (6 derniers mois)</span>
            </div>
            <div class="card-body">
                <canvas id="chartMouvements" height="90"></canvas>
            </div>
        </div>
    </div>

    <!-- Alertes rapides -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <i class="bi bi-bell text-danger me-2"></i>Articles en alerte
            </div>
            <div class="card-body p-0">
                @forelse($alertesArticles as $art)
                <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                    <div>
                        <div class="fw-semibold small">{{ Str::limit($art->nom, 25) }}</div>
                        <div class="text-muted" style="font-size:0.75rem">{{ $art->reference }}</div>
                    </div>
                    <span class="badge {{ $art->quantite_stock <= 0 ? 'badge-rupture' : 'badge-alerte' }} rounded-pill px-2">
                        {{ $art->quantite_stock }} {{ $art->unite }}
                    </span>
                </div>
                @empty
                <div class="text-center py-4 text-muted small">
                    <i class="bi bi-check-circle-fill text-success fs-4 d-block mb-1"></i>
                    Aucune alerte
                </div>
                @endforelse
            </div>
            @if($alertesArticles->count())
            <div class="card-footer bg-white text-center">
                <a href="{{ route('alertes.index') }}" class="btn btn-sm btn-outline-danger">Voir toutes les alertes</a>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <!-- Top articles -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white py-3">
                <i class="bi bi-trophy text-warning me-2"></i>Top articles vendus ce mois
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Article</th><th>Qté</th><th>CA</th></tr></thead>
                    <tbody>
                    @forelse($topArticles as $item)
                    <tr>
                        <td>
                            <div class="fw-semibold small">{{ $item->nom }}</div>
                            <div class="text-muted" style="font-size:0.75rem">{{ $item->reference }}</div>
                        </td>
                        <td>{{ $item->total_vendu }}</td>
                        <td class="text-success fw-semibold">{{ number_format($item->chiffre_affaires, 0, ',', ' ') }} F</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">Aucune vente ce mois</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Derniers mouvements -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white py-3">
                <i class="bi bi-clock-history text-info me-2"></i>Derniers mouvements
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Article</th><th>Type</th><th>Qté</th><th>Date</th></tr></thead>
                    <tbody>
                    @forelse($derniersMouvements as $mvt)
                    <tr>
                        <td class="small fw-semibold">{{ Str::limit($mvt->article->nom ?? '-', 20) }}</td>
                        <td>
                            @if($mvt->type === 'entree')
                                <span class="badge badge-normal">Entrée</span>
                            @elseif($mvt->type === 'sortie')
                                <span class="badge badge-rupture">Sortie</span>
                            @else
                                <span class="badge bg-secondary text-white">Ajust.</span>
                            @endif
                        </td>
                        <td class="{{ $mvt->quantite > 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                            {{ $mvt->quantite > 0 ? '+' : '' }}{{ $mvt->quantite }}
                        </td>
                        <td class="text-muted small">{{ $mvt->created_at->format('d/m H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">Aucun mouvement</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    <!--<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
    <script src="{{ asset('vendor/chartjs/chart.min.js') }}"></script>
const ctx = document.getElementById('chartMouvements').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($graphData['mois']) !!},
        datasets: [
            {
                label: 'Entrées (F CFA)',
                data: {!! json_encode($graphData['entrees']) !!},
                backgroundColor: 'rgba(37,99,235,0.7)',
                borderRadius: 4,
            },
            {
                label: 'Sorties (F CFA)',
                data: {!! json_encode($graphData['sorties']) !!},
                backgroundColor: 'rgba(16,185,129,0.7)',
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endpush
