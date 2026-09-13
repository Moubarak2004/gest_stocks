<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reçu de sortie {{ $sortie->numero }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #1f2937;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #ef4444;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #ef4444;
            font-size: 1.8rem;
        }
        .header small {
            color: #6b7280;
        }
        .info {
            background: #fef2f2;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .info-item {
            font-size: 0.9rem;
        }
        .info-item strong {
            color: #b91c1c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #f3f4f6;
            padding: 10px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #e5e7eb;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #f3f4f6;
        }
        .total {
            text-align: right;
            font-size: 1.1rem;
            font-weight: bold;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #ef4444;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.7rem;
            color: #9ca3af;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🧾 REÇU DE VENTE / SORTIE</h1>
        <small>Document valant justificatif</small>
    </div>

    <div class="info">
        <div class="info-item"><strong>N° facture</strong> {{ $sortie->numero }}</div>
        <div class="info-item"><strong>Date</strong> {{ $sortie->date_sortie->format('d/m/Y') }}</div>
        <div class="info-item"><strong>Client</strong> {{ $sortie->client_nom ?? 'Comptant' }}</div>
    </div>

    <table>
        <thead>
            <tr><th>Article</th><th>Qté</th><th>Prix unit.</th><th>Montant</th></tr>
        </thead>
        <tbody>
            @foreach($sortie->details as $d)
            <tr>
                <td>{{ $d->article->nom }}<br><span style="font-size:0.7rem;color:#6b7280;">{{ $d->article->reference }}</span></td>
                <td>{{ $d->quantite }} {{ $d->article->unite }}</td>
                <td>{{ number_format($d->prix_unitaire, 0, ',', ' ') }} F</td>
                <td>{{ number_format($d->montant, 0, ',', ' ') }} F</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Sous-total : {{ number_format($sortie->montant_total, 0, ',', ' ') }} F<br>
        @if($sortie->remise > 0)
            Remise : -{{ number_format($sortie->remise, 0, ',', ' ') }} F<br>
        @endif
        <span style="color:#ef4444;">Net à payer : {{ number_format($sortie->montant_total - $sortie->remise, 0, ',', ' ') }} F</span><br>
        @if($sortie->montant_recu)
            Reçu : {{ number_format($sortie->montant_recu, 0, ',', ' ') }} F<br>
            Monnaie : {{ number_format($sortie->monnaie, 0, ',', ' ') }} F
        @endif
    </div>

    @if($sortie->notes)
        <div style="margin-top:20px; background:#f9fafb; padding:10px; border-radius:8px;">
            <strong>📝 Notes :</strong> {{ $sortie->notes }}
        </div>
    @endif

    <div class="footer">
        Merci de votre confiance – StockPro<br>
        Document généré le {{ now()->format('d/m/Y H:i') }}
    </div>
</div>
</body>
</html>