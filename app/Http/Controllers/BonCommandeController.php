<?php

namespace App\Http\Controllers;

use App\Models\BonCommande;
use App\Models\BonCommandeDetail;
use App\Models\Article;
use App\Models\Fournisseur;
use App\Models\Entree;
use App\Models\EntreeDetail;
use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class BonCommandeController extends Controller
{
    public function index(Request $request)
    {
        $query = BonCommande::with(['fournisseur', 'user'])->orderByDesc('date_commande');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('fournisseur')) {
            $query->where('fournisseur_id', $request->fournisseur);
        }

        $bons         = $query->paginate(20)->withQueryString();
        $fournisseurs = Fournisseur::where('actif', true)->orderBy('nom')->get();

        return view('bons.index', compact('bons', 'fournisseurs'));
    }

    public function create()
    {
        $fournisseurs = Fournisseur::where('actif', true)->orderBy('nom')->get();
        $numero       = BonCommande::genererNumero();

        // Articles dont le stock est bas pour suggestion
        $articlesEnAlerte = Article::where('actif', true)
            ->whereRaw('quantite_stock <= stock_minimum')
            ->with('fournisseur')
            ->get();

        return view('bons.create', compact('fournisseurs', 'numero', 'articlesEnAlerte'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fournisseur_id'       => 'nullable|exists:fournisseurs,id',
            'date_commande'        => 'required|date',
            'date_livraison_prevue'=> 'nullable|date|after_or_equal:date_commande',
            'articles'             => 'required|array|min:1',
            'articles.*.article_id'    => 'required|exists:articles,id',
            'articles.*.quantite'      => 'required|integer|min:1',
            'articles.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $montantTotal = 0;
            foreach ($request->articles as $ligne) {
                $montantTotal += $ligne['quantite'] * $ligne['prix_unitaire'];
            }

            $bon = BonCommande::create([
                'numero'                => BonCommande::genererNumero(),
                'fournisseur_id'        => $request->fournisseur_id,
                'user_id'               => auth()->id(),
                'date_commande'         => $request->date_commande,
                'date_livraison_prevue' => $request->date_livraison_prevue,
                'montant_total'         => $montantTotal,
                'statut'                => $request->statut ?? 'brouillon',
                'notes'                 => $request->notes,
            ]);

            foreach ($request->articles as $ligne) {
                BonCommandeDetail::create([
                    'bon_commande_id'   => $bon->id,
                    'article_id'        => $ligne['article_id'],
                    'quantite_commandee'=> $ligne['quantite'],
                    'quantite_recue'    => 0,
                    'prix_unitaire'     => $ligne['prix_unitaire'],
                    'montant'           => $ligne['quantite'] * $ligne['prix_unitaire'],
                ]);
            }
        });

        return redirect()->route('bons.index')->with('success', 'Bon de commande créé avec succès.');
    }

    public function show(BonCommande $bon)
    {
        $bon->load(['fournisseur', 'user', 'details.article']);
        return view('bons.show', compact('bon'));
    }

    public function edit(BonCommande $bon)
    {
        if (!in_array($bon->statut, ['brouillon'])) {
            return back()->with('error', 'Seuls les bons en brouillon peuvent être modifiés.');
        }
        $fournisseurs = Fournisseur::where('actif', true)->orderBy('nom')->get();
        $bon->load('details.article');
        return view('bons.edit', compact('bon', 'fournisseurs'));
    }

  /*  public function update(Request $request, BonCommande $bon)
    {
        $request->validate([
            'statut' => 'required|in:brouillon,envoyé,annulé',
        ]);

        $bon->update(['statut' => $request->statut, 'notes' => $request->notes]);
        return redirect()->route('bons.show', $bon)->with('success', 'Bon de commande mis à jour.');
    }   */
   public function update(Request $request, BonCommande $bon)
{
    if (!in_array($bon->statut, ['brouillon'])) {
        return back()->with('error', 'Seuls les bons en brouillon peuvent être modifiés.');
    }

    $request->validate([
        'date_commande'        => 'required|date',
        'date_livraison_prevue'=> 'nullable|date|after_or_equal:date_commande',
        'fournisseur_id'       => 'nullable|exists:fournisseurs,id',
        'statut'               => 'required|in:brouillon,envoyé,annulé',
        'notes'                => 'nullable|string',
    ]);

    $bon->update($request->only(['date_commande', 'date_livraison_prevue', 'fournisseur_id', 'statut', 'notes']));

    return redirect()->route('bons.show', $bon)->with('success', 'Bon de commande mis à jour.');
}

    // Réceptionner un bon de commande = créer une entrée de stock
    public function receptionner(Request $request, BonCommande $bon)
    {
        $request->validate([
            'articles'             => 'required|array',
            'articles.*.detail_id' => 'required|exists:bon_commande_details,id',
            'articles.*.quantite'  => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request, $bon) {
            $montantTotal = 0;
            $lignesEntree = [];

            foreach ($request->articles as $ligne) {
                if ($ligne['quantite'] <= 0) continue;

                $detail = BonCommandeDetail::find($ligne['detail_id']);
                $detail->increment('quantite_recue', $ligne['quantite']);
                $montantTotal += $ligne['quantite'] * $detail->prix_unitaire;

                $lignesEntree[] = [
                    'article_id'    => $detail->article_id,
                    'quantite'      => $ligne['quantite'],
                    'prix_unitaire' => $detail->prix_unitaire,
                    'montant'       => $ligne['quantite'] * $detail->prix_unitaire,
                ];
            }

            if (!empty($lignesEntree)) {
                $entree = Entree::create([
                    'numero'         => Entree::genererNumero(),
                    'fournisseur_id' => $bon->fournisseur_id,
                    'user_id'        => auth()->id(),
                    'date_entree'    => now()->toDateString(),
                    'reference_bon'  => $bon->numero,
                    'montant_total'  => $montantTotal,
                    'statut'         => 'validé',
                    'notes'          => 'Réception BC ' . $bon->numero,
                ]);

                foreach ($lignesEntree as $ligne) {
                    EntreeDetail::create(array_merge(['entree_id' => $entree->id], $ligne));

                    $article    = Article::find($ligne['article_id']);
                    $stockAvant = $article->quantite_stock;
                    $article->increment('quantite_stock', $ligne['quantite']);

                    MouvementStock::create([
                        'article_id'         => $article->id,
                        'user_id'            => auth()->id(),
                        'type'               => 'entree',
                        'quantite'           => $ligne['quantite'],
                        'stock_avant'        => $stockAvant,
                        'stock_apres'        => $article->fresh()->quantite_stock,
                        'reference_document' => $bon->numero,
                        'motif'              => 'Réception BC',
                    ]);
                }
            }

            // Vérifier si tout est reçu
            $toutRecu = $bon->details->every(fn($d) => $d->fresh()->quantite_recue >= $d->quantite_commandee);
            $bon->update(['statut' => $toutRecu ? 'reçu' : 'reçu_partiel']);
        });

        return redirect()->route('bons.show', $bon)->with('success', 'Réception enregistrée et stock mis à jour.');
    }

    public function destroy(BonCommande $bon)
    {
        if ($bon->statut !== 'brouillon') {
            return back()->with('error', 'Seuls les bons en brouillon peuvent être supprimés.');
        }
        $bon->delete();
        return redirect()->route('bons.index')->with('success', 'Bon de commande supprimé.');
    }

    public function pdf(BonCommande $bon)
{
    $bon->load(['fournisseur', 'user', 'details.article']);
    $pdf = Pdf::loadView('bons.pdf', compact('bon'));
    return $pdf->download('bon_commande_'.$bon->numero.'.pdf');
}
}
