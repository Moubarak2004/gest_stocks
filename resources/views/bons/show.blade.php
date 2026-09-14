@extends('layouts.app')
@section('title', 'Bon ' . $bon->numero)
@section('page-title', 'Bon de commande ' . $bon->numero)

@section('content')
<div class="row">
<div class="col-lg-4">
    <div class="card mb-3">
        <div class="card-header bg-white py-3 fw-semibold"><i class="bi bi-info-circle me-2"></i>Informations</div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table table-sm mb-0">
                <tr><td class="text-muted">N° Bon</td><td class="fw-bold text-primary">{{ $bon->numero }}</td></tr>
                <tr><td class="text-muted">Date commande</td><td>{{ $bon->date_commande->format('d/m/Y') }}</td></tr>
                <tr><td class="text-muted">Livraison prévue</td><td>{{ $bon->date_livraison_prevue ? $bon->date_livraison_prevue->format('d/m/Y') : '-' }}</td></tr>
                <tr><td class="text-muted">Fournisseur</td><td>{{ $bon->fournisseur->nom ?? 'N/A' }}</td></tr>
                <tr><td class="text-muted">Montant total</td><td class="fw-bold text-primary">{{ number_format($bon->montant_total, 0, ',', ' ') }} F</td></tr>
                <tr><td class="text-muted">Statut</td>
                    <td>
                        @php $colors = ['brouillon'=>'bg-secondary text-white','envoyé'=>'bg-primary text-white','reçu_partiel'=>'badge-alerte','reçu'=>'badge-normal','annulé'=>'badge-rupture'] @endphp
                        <span class="badge {{ $colors[$bon->statut] ?? 'bg-secondary' }}">{{ ucfirst(str_replace('_', ' ', $bon->statut)) }}</span>
                    </td>
                </tr>
                <tr><td class="text-muted">Créé par</td><td>{{ $bon->user->name ?? '-' }}</td></tr>
                <tr><td class="text-muted">Date saisie</td><td>{{ $bon->created_at->format('d/m/Y H:i') }}</td></tr>
            </table>
            </div>
        </div>
    </div>
    @if($bon->notes)
    <div class="card mb-3">
        <div class="card-body small"><strong>Notes :</strong> {{ $bon->notes }}</div>
    </div>
    @endif

    <!-- Actions rapides -->
    <div class="card">
        <div class="card-body d-flex flex-column gap-2">
            @if($bon->statut === 'brouillon')
            <form action="{{ route('bons.update', $bon) }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="statut" value="envoyé">
                <button class="btn btn-primary w-100"><i class="bi bi-send me-1"></i>Marquer comme envoyé</button>
            </form>
            <a href="{{ route('bons.edit', $bon) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Modifier
            </a>
            <form action="{{ route('bons.destroy', $bon) }}" method="POST" onsubmit="return confirm('Supprimer ce bon ?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger w-100"><i class="bi bi-trash me-1"></i>Supprimer</button>
            </form>
            @elseif($bon->statut === 'envoyé')
            <form action="{{ route('bons.update', $bon) }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="statut" value="annulé">
                <button class="btn btn-outline-danger w-100" onclick="return confirm('Annuler ce bon ?')">
                    <i class="bi bi-x-circle me-1"></i>Annuler le bon
                </button>
            </form>
            @endif
            <a href="{{ route('bons.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>
</div>

<div class="col-lg-8">
    <div class="card mb-3">
        <div class="card-header bg-white py-3 fw-semibold"><i class="bi bi-list-ul me-2"></i>Articles commandés</div>
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Article</th><th>Commandé</th><th>Reçu</th><th>Reste</th><th>Prix unit.</th><th>Montant</th></tr></thead>
                <tbody>
                @foreach($bon->details as $d)
                @php $reste = $d->quantite_commandee - $d->quantite_recue; @endphp
                <tr>
                    <td>
                        <div class="fw-semibold small">{{ $d->article->nom ?? '-' }}</div>
                        <div class="text-muted" style="font-size:0.75rem">{{ $d->article->reference ?? '' }}</div>
                    </td>
                    <td class="fw-semibold">{{ $d->quantite_commandee }}</td>
                    <td class="text-success fw-semibold">{{ $d->quantite_recue }}</td>
                    <td class="{{ $reste > 0 ? 'text-danger' : 'text-success' }} fw-semibold">{{ $reste }}</td>
                    <td>{{ number_format($d->prix_unitaire, 0, ',', ' ') }} F</td>
                    <td class="text-primary fw-semibold">{{ number_format($d->montant, 0, ',', ' ') }} F</td>
                </tr>
                @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="5" class="text-end fw-bold">TOTAL</td>
                        <td class="fw-bold text-primary">{{ number_format($bon->montant_total, 0, ',', ' ') }} F</td>
                    </tr>
                </tfoot>
            </table>
            </div>
        </div>
    </div>

    @if(in_array($bon->statut, ['envoyé', 'reçu_partiel']))
    <!-- Formulaire de réception -->
    <div class="card border-success">
        <div class="card-header bg-success text-white py-3 fw-semibold">
            <i class="bi bi-box-arrow-in-down me-2"></i>Réceptionner les articles
        </div>
        <div class="card-body">
            <form action="{{ route('bons.receptionner', $bon) }}" method="POST">
                @csrf
                <p class="text-muted small mb-3">Saisissez les quantités réellement reçues pour chaque article :</p>
                <div class="table-responsive">
                <table class="table table-sm mb-3">
                    <thead><tr><th>Article</th><th>Commandé</th><th>Déjà reçu</th><th>Qté à réceptionner</th></tr></thead>
                    <tbody>
                    @foreach($bon->details as $d)
                    @php $restant = $d->quantite_commandee - $d->quantite_recue; @endphp
                    @if($restant > 0)
                    <tr>
                        <td>
                            <input type="hidden" name="articles[{{ $loop->index }}][detail_id]" value="{{ $d->id }}">
                            <div class="fw-semibold small">{{ $d->article->nom ?? '-' }}</div>
                        </td>
                        <td>{{ $d->quantite_commandee }}</td>
                        <td class="text-muted">{{ $d->quantite_recue }}</td>
                        <td>
                            <input type="number" name="articles[{{ $loop->index }}][quantite]"
                                   class="form-control form-control-sm" style="width:100px"
                                   min="0" max="{{ $restant }}" value="{{ $restant }}">
                        </td>
                    </tr>
                    @endif
                    @endforeach
                    </tbody>
                </table>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i>Valider la réception
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
</div>
<a href="{{ route('bons.pdf', $bon) }}" class="btn btn-sm btn-primary">
    <i class="bi bi-printer me-1"></i> PDF
</a>
@endsection