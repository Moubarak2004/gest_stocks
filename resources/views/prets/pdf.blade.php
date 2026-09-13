<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Prêt {{ $pret->numero }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            line-height: 1.5;
            color: #1f2937;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
            color: #2563eb;
        }
        .header p {
            margin: 5px 0 0;
            color: #6b7280;
            font-size: 0.8rem;
        }
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: 600;
            width: 35%;
            padding: 8px 0;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
        }
        .info-value {
            display: table-cell;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .notes {
            background: #f9fafb;
            padding: 12px;
            border-left: 4px solid #3b82f6;
            margin: 20px 0;
            font-style: italic;
            border-radius: 6px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.7rem;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>📄 CERTIFICAT DE PRÊT</h1>
        <p>Document officiel – StockPro</p>
    </div>

    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">N° du prêt</div>
            <div class="info-value"><strong>{{ $pret->numero }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Date du prêt</div>
            <div class="info-value">{{ $pret->date_pret->format('d/m/Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Retour prévu</div>
            <div class="info-value">{{ $pret->date_retour_prevue ? $pret->date_retour_prevue->format('d/m/Y') : 'Non spécifiée' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Article</div>
            <div class="info-value">{{ $pret->article->nom }} <span style="color:#6b7280;">({{ $pret->article->reference }})</span></div>
        </div>
        <div class="info-row">
            <div class="info-label">Quantité prêtée</div>
            <div class="info-value">{{ $pret->quantite }} {{ $pret->article->unite }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Emprunteur</div>
            <div class="info-value">{{ $pret->emprunteur_nom }} @if($pret->emprunteur_telephone)<br><small>📞 {{ $pret->emprunteur_telephone }}</small>@endif</div>
        </div>
        <div class="info-row">
            <div class="info-label">Statut</div>
            <div class="info-value">
                @php
                    $badgeColor = match($pret->statut) {
                        'en_cours' => '#3b82f6',
                        'retard'   => '#f59e0b',
                        'rendu'    => '#10b981',
                        'annule'   => '#ef4444',
                        default    => '#6b7280'
                    };
                @endphp
                <span style="background:{{ $badgeColor }}; color:white; padding:2px 8px; border-radius:20px; font-size:0.7rem; font-weight:bold;">
                    {{ ucfirst(str_replace('_', ' ', $pret->statut)) }}
                </span>
            </div>
        </div>
        @if($pret->date_retour_reelle)
        <div class="info-row">
            <div class="info-label">Retour effectué le</div>
            <div class="info-value">{{ $pret->date_retour_reelle->format('d/m/Y') }}</div>
        </div>
        @endif
    </div>

    @if($pret->notes)
    <div class="notes">
        <strong>✏️ Notes :</strong><br>
        {{ nl2br(e($pret->notes)) }}
    </div>
    @endif

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y \à H:i') }} – StockPro Gestion de stocks
    </div>
</div>
</body>
</html>