<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Entrée {{ $entree->numero }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f9fafb;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            padding: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #10b981;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h1 {
            margin: 0;
            color: #047857;
            font-size: 1.6rem;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0 0;
        }
        .info-grid {
            display: flex;
            flex-wrap: wrap;
            background: #f0fdf4;
            padding: 12px 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            gap: 15px;
        }
        .info-card {
            flex: 1;
            min-width: 150px;
        }
        .info-card .label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #4b5563;
        }
        .info-card .value {
            font-weight: bold;
            font-size: 1rem;
            color: #065f46;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #e5e7eb;
            padding: 10px;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .total {
            text-align: right;
            font-size: 1.1rem;
            font-weight: bold;
            background: #f0fdf4;
            padding: 10px;
            border-radius: 8px;
            margin-top: 15px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.7rem;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>📥 BON D’ENTRÉE DE STOCK</h1>
        <p>Réception marchandise – StockPro</p>
    </div>

    <div class="info-grid">
        <div class="info-card">
            <div class="label">N° d’entrée</div>
            <div class="value">{{ $entree->numero }}</div>
        </div>
        <div class="info-card">
            <div class="label">Date</div>
            <div class="value">{{ $entree->date_entree->format('d/m/Y') }}</div>
        </div>
        <div class="info-card">
            <div class="label">Fournisseur</div>
            <div class="value">{{ $entree->fournisseur->nom ?? 'N/A' }}</div>
        </div>
        <div class="info-card">
            <div class="label">Bon livraison</div>
            <div class="value">{{ $entree->reference_bon ?? '-' }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr><th>Article</th><th>Référence</th><th>Quantité</th><th>Prix unit.</th><th>Montant</th></tr>
        </thead>
        <tbody>
        @foreach($entree->details as $d)
        <tr>
            <td>{{ $d->article->nom }}</td>
            <td>{{ $d->article->reference }}</td>
            <td>{{ $d->quantite }} {{ $d->article->unite }}</td>
            <td>{{ number_format($d->prix_unitaire, 0, ',', ' ') }} F</td>
            <td>{{ number_format($d->montant, 0, ',', ' ') }} F</td>
        </tr>
        @endforeach
        </tbody>
    </table>

    <div class="total">
        Total entrée : {{ number_format($entree->montant_total, 0, ',', ' ') }} F
    </div>

    @if($entree->notes)
        <div style="margin-top:20px; background:#eff6ff; padding:12px; border-radius:8px;">
            <strong>📌 Notes :</strong> {{ $entree->notes }}
        </div>
    @endif

    <div class="footer">
        Document validé par {{ $entree->user->name ?? 'Système' }} – Généré le {{ now()->format('d/m/Y H:i') }}
    </div>
</div>
</body>
</html>