<?php

namespace App\Http\Controllers;

use App\Models\Entree;
use App\Models\EntreeDetail;
use App\Models\Article;
use App\Models\Fournisseur;
use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class EntreeController extends Controller
{
    public function index(Request $request)
    {
        $query = Entree::with(['fournisseur', 'user'])->orderByDesc('date_entree');

        if ($request->filled('search')) {
            $query->where('numero', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('fournisseur')) {
            $query->where('fournisseur_id', $request->fournisseur);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('date_debut')) {
            $query->where('date_entree', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->where('date_entree', '<=', $request->date_fin);
        }

        $entrees     = $query->paginate(20)->withQueryString();
        $fournisseurs = Fournisseur::where('actif', true)->orderBy('nom')->get();

        return view('entrees.index', compact('entrees', 'fournisseurs'));
    }

    public function create()
    {
        $fournisseurs = Fournisseur::where('actif', true)->orderBy('nom')->get();
        $numero       = Entree::genererNumero();
        return view('entrees.create', compact('fournisseurs', 'numero'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'date_entree'    => 'required|date',
            'articles'       => 'required|array|min:1',
            'articles.*.article_id'    => 'required|exists:articles,id',
            'articles.*.quantite'      => 'required|integer|min:1',
            'articles.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $montantTotal = 0;
            foreach ($request->articles as $ligne) {
                $montantTotal += $ligne['quantite'] * $ligne['prix_unitaire'];
            }

            $entree = Entree::create([
                'numero'          => Entree::genererNumero(),
                'fournisseur_id'  => $request->fournisseur_id,
                'user_id'         => auth()->id(),
                'date_entree'     => $request->date_entree,
                'reference_bon'   => $request->reference_bon,
                'montant_total'   => $montantTotal,
                'statut'          => 'brouillon',  // CHANGÉ : on crée en brouillon
                'notes'           => $request->notes,
            ]);

            foreach ($request->articles as $ligne) {
                EntreeDetail::create([
                    'entree_id'     => $entree->id,
                    'article_id'    => $ligne['article_id'],
                    'quantite'      => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'montant'       => $ligne['quantite'] * $ligne['prix_unitaire'],
                ]);

                // On ne met pas à jour le stock ici – cela se fera lors de la validation
                // On n'enregistre pas non plus de mouvement de stock
            }
        });

        return redirect()->route('entrees.index')->with('success', 'Entrée créée en mode brouillon. Vous pouvez la modifier avant validation.');
    }

    public function show(Entree $entree)
    {
        $entree->load(['fournisseur', 'user', 'details.article']);
        return view('entrees.show', compact('entree'));
    }

    public function edit(Entree $entree)
    {
        if ($entree->statut !== 'brouillon') {
            return back()->with('error', 'Seules les entrées en brouillon peuvent être modifiées.');
        }
        $fournisseurs = Fournisseur::where('actif', true)->get();
        return view('entrees.edit', compact('entree', 'fournisseurs'));
    }

    public function update(Request $request, Entree $entree)
    {
        if ($entree->statut !== 'brouillon') {
            return back()->with('error', 'Seules les entrées en brouillon peuvent être modifiées.');
        }

        $request->validate([
            'date_entree'    => 'required|date',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'reference_bon'  => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
        ]);

        $entree->update($request->only(['date_entree', 'fournisseur_id', 'reference_bon', 'notes']));

        return redirect()->route('entrees.show', $entree)->with('success', 'Entrée modifiée avec succès.');
    }

    // NOUVELLE MÉTHODE : validation de l'entrée (mise à jour du stock)
    public function valider(Entree $entree)
    {
        if ($entree->statut !== 'brouillon') {
            return back()->with('error', 'Seules les entrées en brouillon peuvent être validées.');
        }

        DB::transaction(function () use ($entree) {
            foreach ($entree->details as $detail) {
                $article = $detail->article;
                $stockAvant = $article->quantite_stock;
                $article->increment('quantite_stock', $detail->quantite);
                $article->update(['prix_achat' => $detail->prix_unitaire]);

                MouvementStock::create([
                    'article_id'         => $article->id,
                    'user_id'            => auth()->id(),
                    'type'               => 'entree',
                    'quantite'           => $detail->quantite,
                    'stock_avant'        => $stockAvant,
                    'stock_apres'        => $article->quantite_stock,
                    'reference_document' => $entree->numero,
                    'motif'              => 'Validation entrée',
                ]);

                ArticleController::verifierAlertes($article);
            }
            $entree->update(['statut' => 'validé']);
        });

        return redirect()->route('entrees.show', $entree)->with('success', 'Entrée validée et stock mis à jour.');
    }

    public function destroy(Entree $entree)
    {
        if ($entree->statut === 'validé') {
            return back()->with('error', 'Impossible de supprimer une entrée validée. Annulez-la d\'abord.');
        }
        $entree->delete();
        return redirect()->route('entrees.index')->with('success', 'Entrée supprimée.');
    }

    public function annuler(Entree $entree)
    {
        if ($entree->statut !== 'validé') {
            return back()->with('error', 'Cette entrée ne peut pas être annulée.');
        }

        DB::transaction(function () use ($entree) {
            foreach ($entree->details as $detail) {
                $article    = $detail->article;
                $stockAvant = $article->quantite_stock;
                $article->decrement('quantite_stock', $detail->quantite);

                MouvementStock::create([
                    'article_id'         => $article->id,
                    'user_id'            => auth()->id(),
                    'type'               => 'ajustement',
                    'quantite'           => -$detail->quantite,
                    'stock_avant'        => $stockAvant,
                    'stock_apres'        => $article->fresh()->quantite_stock,
                    'reference_document' => $entree->numero,
                    'motif'              => 'Annulation entrée ' . $entree->numero,
                ]);

                ArticleController::verifierAlertes($article->fresh());
            }
            $entree->update(['statut' => 'annulé']);
        });

        return back()->with('success', 'Entrée annulée. Le stock a été mis à jour.');
    }

    public function pdf(Entree $entree)
    {
        $entree->load(['fournisseur', 'user', 'details.article']);
        $pdf = Pdf::loadView('entrees.pdf', compact('entree'));
        return $pdf->download('entree_'.$entree->numero.'.pdf');
    }
}