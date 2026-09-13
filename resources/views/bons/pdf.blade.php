<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bon de commande {{ $bon->numero }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #1f2937;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #1e40af;
        }
        .badge {
            display: inline-block;
            background: #e0f2fe;
            color: #0369a1;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: bold;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }
        .info-label {
            width: 160px;
            font-weight: 600;
            color: #374151;
        }
        .info-value {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #f1f5f9;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #cbd5e1;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .total {
            text-align: right;
            font-weight: bold;
            font-size: 1.1rem;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #2563eb;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 0.7rem;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>📦 BON DE COMMANDE</h1>
        <div class="badge">{{ ucfirst(str_replace('_', ' ', $bon->statut)) }}</div>
    </div>

    <div class="info-row">
        <div class="info-label">N° du bon</div>
        <div class="info-value"><strong>{{ $bon->numero }}</strong></div>
    </div>
    <div class="info-row">
        <div class="info-label">Date commande</div>
        <div class="info-value">{{ $bon->date_commande->format('d/m/Y') }}</div>
    </div>
    <div class="info-row">
        <div class="info-label">Livraison prévue</div>
        <div class="info-value">{{ $bon->date_livraison_prevue ? $bon->date_livraison_prevue->format('d/m/Y') : 'À convenir' }}</div>
    </div>
    <div class="info-row">
        <div class="info-label">Fournisseur</div>
        <div class="info-value">{{ $bon->fournisseur->nom ?? 'N/A' }}</div>
    </div>

    <table>
        <thead>
            <tr><th>Article</th><th>Référence</th><th>Qté commandée</th><th>Déjà reçu</th><th>Prix unit.</th><th>Montant</th></tr>
        </thead>
        <tbody>
        @foreach($bon->details as $d)
        <tr>
            <td>{{ $d->article->nom }}</td>
            <td>{{ $d->article->reference }}</td>
            <td>{{ $d->quantite_commandee }}</td>
            <td>{{ $d->quantite_recue }}</td>
            <td>{{ number_format($d->prix_unitaire, 0, ',', ' ') }} F</td>
            <td>{{ number_format($d->montant, 0, ',', ' ') }} F</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    <div class="total">
        Total commande : {{ number_format($bon->montant_total, 0, ',', ' ') }} F
    </div>

    @if($bon->notes)
        <div style="background:#f8fafc; padding:12px; border-radius:8px; margin-top:15px;">
            <strong>✍️ Remarques :</strong><br> {{ nl2br(e($bon->notes)) }}
        </div>
    @endif

    <div class="footer">
        StockPro – Gestion d’approvisionnement<br>
        Généré le {{ now()->format('d/m/Y \à H:i') }}
    </div>
</div>
</body>
</html>