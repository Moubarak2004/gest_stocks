<?php

namespace App\Http\Controllers;

use App\Models\Sortie;
use App\Models\SortieDetail;
use App\Models\Article;
use App\Models\MouvementStock;
use App\Models\Alerte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SortieController extends Controller
{
    public function index(Request $request)
    {
        $query = Sortie::with(['user'])->orderByDesc('date_sortie');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('numero', 'like', '%' . $request->search . '%')
                  ->orWhere('client_nom', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('date_debut')) {
            $query->where('date_sortie', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->where('date_sortie', '<=', $request->date_fin);
        }

        $sorties = $query->paginate(20)->withQueryString();

        return view('sorties.index', compact('sorties'));
    }

    public function create()
    {
        $numero = Sortie::genererNumero();
        return view('sorties.create', compact('numero'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_sortie'  => 'required|date',
            'type'         => 'required|in:vente,retour_fournisseur,perte,transfert',
            'articles'     => 'required|array|min:1',
            'articles.*.article_id'    => 'required|exists:articles,id',
            'articles.*.quantite'      => 'required|integer|min:1',
            'articles.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        // On vérifie le stock uniquement lors de la validation, pas à la création
        // On peut éventuellement avertir mais on ne bloque pas

        DB::transaction(function () use ($request) {
            $montantTotal = 0;
            foreach ($request->articles as $ligne) {
                $montantTotal += $ligne['quantite'] * $ligne['prix_unitaire'];
            }

            $remise = $request->remise ?? 0;

            $sortie = Sortie::create([
                'numero'           => Sortie::genererNumero(),
                'user_id'          => auth()->id(),
                'date_sortie'      => $request->date_sortie,
                'client_nom'       => $request->client_nom,
                'client_telephone' => $request->client_telephone,
                'type'             => $request->type,
                'montant_total'    => $montantTotal,
                'montant_recu'     => $request->montant_recu ?? $montantTotal,
                'remise'           => $remise,
                'statut'           => 'brouillon',  // CHANGÉ : on crée en brouillon
                'notes'            => $request->notes,
            ]);

            foreach ($request->articles as $ligne) {
                SortieDetail::create([
                    'sortie_id'     => $sortie->id,
                    'article_id'    => $ligne['article_id'],
                    'quantite'      => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'montant'       => $ligne['quantite'] * $ligne['prix_unitaire'],
                ]);

                // Pas de mise à jour du stock ici
            }
        });

        return redirect()->route('sorties.index')->with('success', 'Sortie créée en mode brouillon. Vous pouvez la modifier avant validation.');
    }

    public function show($id)
    {
        $sortie = Sortie::with(['user', 'details.article'])->findOrFail($id);
        return view('sorties.show', compact('sortie'));
    }

    public function edit(Sortie $sortie)
    {
        if ($sortie->statut !== 'brouillon') {
            return back()->with('error', 'Seules les sorties en brouillon peuvent être modifiées.');
        }
        return view('sorties.edit', compact('sortie'));
    }

    public function update(Request $request, Sortie $sortie)
    {
        if ($sortie->statut !== 'brouillon') {
            return back()->with('error', 'Seules les sorties en brouillon peuvent être modifiées.');
        }

        $request->validate([
            'date_sortie'      => 'required|date',
            'client_nom'       => 'nullable|string|max:255',
            'client_telephone' => 'nullable|string|max:20',
            'notes'            => 'nullable|string',
        ]);

        $sortie->update($request->only(['date_sortie', 'client_nom', 'client_telephone', 'notes']));

        return redirect()->route('sorties.show', $sortie)->with('success', 'Sortie modifiée avec succès.');
    }

    // NOUVELLE MÉTHODE : validation de la sortie (vérification stock + mise à jour)
    public function valider(Sortie $sortie)
    {
        if ($sortie->statut !== 'brouillon') {
            return back()->with('error', 'Seules les sorties en brouillon peuvent être validées.');
        }

        // Vérification des stocks disponibles
        foreach ($sortie->details as $detail) {
            $article = $detail->article;
            if ($article->quantite_stock < $detail->quantite) {
                return back()->with('error', "Stock insuffisant pour l'article {$article->nom}. Disponible : {$article->quantite_stock} {$article->unite}");
            }
        }

        DB::transaction(function () use ($sortie) {
            foreach ($sortie->details as $detail) {
                $article = $detail->article;
                $stockAvant = $article->quantite_stock;
                $article->decrement('quantite_stock', $detail->quantite);

                MouvementStock::create([
                    'article_id'         => $article->id,
                    'user_id'            => auth()->id(),
                    'type'               => 'sortie',
                    'quantite'           => -$detail->quantite,
                    'stock_avant'        => $stockAvant,
                    'stock_apres'        => $article->quantite_stock,
                    'reference_document' => $sortie->numero,
                    'motif'              => 'Validation ' . ucfirst($sortie->type),
                ]);

                ArticleController::verifierAlertes($article);
            }
            $sortie->update(['statut' => 'validé']);
        });

        return redirect()->route('sorties.show', $sortie)->with('success', 'Sortie validée et stock mis à jour.');
    }

    public function annuler(Sortie $sortie)
    {
        if ($sortie->statut !== 'validé') {
            return back()->with('error', 'Cette sortie ne peut pas être annulée.');
        }

        DB::transaction(function () use ($sortie) {
            foreach ($sortie->details as $detail) {
                $article    = $detail->article;
                $stockAvant = $article->quantite_stock;
                $article->increment('quantite_stock', $detail->quantite);

                MouvementStock::create([
                    'article_id'         => $article->id,
                    'user_id'            => auth()->id(),
                    'type'               => 'ajustement',
                    'quantite'           => $detail->quantite,
                    'stock_avant'        => $stockAvant,
                    'stock_apres'        => $article->fresh()->quantite_stock,
                    'reference_document' => $sortie->numero,
                    'motif'              => 'Annulation sortie ' . $sortie->numero,
                ]);
            }
            $sortie->update(['statut' => 'annulé']);
        });

        return back()->with('success', 'Sortie annulée. Le stock a été restauré.');
    }

    public function pdf(Sortie $sortie)
    {
        $sortie->load(['details.article', 'user']);
        $pdf = Pdf::loadView('sorties.pdf', compact('sortie'));
        return $pdf->download('reçu_'.$sortie->numero.'.pdf');
    }
}