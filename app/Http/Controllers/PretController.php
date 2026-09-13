<?php

namespace App\Http\Controllers;

use App\Models\Pret;
use App\Models\Article;
use App\Models\Sortie;
use App\Models\SortieDetail;
use App\Models\Entree;
use App\Models\EntreeDetail;
use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PretController extends Controller
{
    public function index(Request $request)
    {
        $query = Pret::with(['article', 'user'])->orderByDesc('date_pret');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('recherche')) {
            $q = $request->recherche;
            $query->where(function ($w) use ($q) {
                $w->where('numero', 'like', "%$q%")
                  ->orWhere('emprunteur_nom', 'like', "%$q%")
                  ->orWhereHas('article', fn($a) => $a->where('nom', 'like', "%$q%"));
            });
        }

        $prets = $query->paginate(20)->withQueryString();
        return view('prets.index', compact('prets'));
    }

    public function create()
    {
        $articles = Article::where('actif', true)
            ->where('quantite_stock', '>', 0)
            ->orderBy('nom')
            ->get(['id', 'nom', 'reference', 'quantite_stock', 'unite']);
        $numero = Pret::genererNumero();
        return view('prets.create', compact('articles', 'numero'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'article_id'      => 'required|exists:articles,id',
            'quantite'        => 'required|integer|min:1',
            'emprunteur_nom'  => 'required|string|max:255',
            'date_pret'       => 'required|date',
            'date_retour_prevue' => 'nullable|date|after_or_equal:date_pret',
        ]);

        $article = Article::findOrFail($request->article_id);

        if ($article->quantite_stock < $request->quantite) {
            return back()->withInput()->withErrors([
                'quantite' => "Stock insuffisant. Il reste {$article->quantite_stock} {$article->unite}."
            ]);
        }

        DB::transaction(function () use ($request, $article) {
            $sortie = Sortie::create([
                'numero'       => Sortie::genererNumero(),
                'user_id'      => auth()->id(),
                'date_sortie'  => $request->date_pret,
                'type'         => 'pret',
                'client_nom'   => $request->emprunteur_nom,
                'client_telephone' => $request->emprunteur_telephone,
                'montant_total'=> 0,
                'montant_recu' => 0,
                'remise'       => 0,
                'statut'       => 'validé',
                'notes'        => "Prêt d'objets - à rendre",
            ]);

            SortieDetail::create([
                'sortie_id'     => $sortie->id,
                'article_id'    => $article->id,
                'quantite'      => $request->quantite,
                'prix_unitaire' => 0,
                'montant'       => 0,
            ]);

            $stockAvant = $article->quantite_stock;
            $article->decrement('quantite_stock', $request->quantite);

            MouvementStock::create([
                'article_id'         => $article->id,
                'user_id'            => auth()->id(),
                'type'               => 'sortie',
                'quantite'           => -$request->quantite,
                'stock_avant'        => $stockAvant,
                'stock_apres'        => $article->quantite_stock,
                'reference_document' => $sortie->numero,
                'motif'              => 'Prêt d\'objet',
            ]);

            $pret = Pret::create([
                'numero'             => Pret::genererNumero(),
                'article_id'         => $article->id,
                'quantite'           => $request->quantite,
                'emprunteur_nom'     => $request->emprunteur_nom,
                'emprunteur_telephone'=> $request->emprunteur_telephone,
                'date_pret'          => $request->date_pret,
                'date_retour_prevue' => $request->date_retour_prevue,
                'statut'             => 'en_cours',
                'notes'              => $request->notes,
                'user_id'            => auth()->id(),
                'reference_sortie'   => $sortie->numero,
            ]);

            ArticleController::verifierAlertes($article);
        });

        return redirect()->route('prets.index')->with('success', 'Prêt enregistré avec succès. Stock mis à jour.');
    }

    public function retourForm(Pret $pret)
    {
        if ($pret->statut !== 'en_cours') {
            return redirect()->route('prets.index')->with('error', 'Ce prêt n\'est pas en cours.');
        }
        return view('prets.retour', compact('pret'));
    }

    public function retourner(Request $request, Pret $pret)
    {
        $request->validate([
            'date_retour_reelle' => 'required|date',
            'notes_retour'       => 'nullable|string',
        ]);

        if ($pret->statut !== 'en_cours') {
            return back()->with('error', 'Ce prêt n\'est pas en cours.');
        }

        DB::transaction(function () use ($request, $pret) {
            $article = $pret->article;

            $entree = Entree::create([
                'numero'         => Entree::genererNumero(),
                'fournisseur_id' => null,
                'user_id'        => auth()->id(),
                'date_entree'    => $request->date_retour_reelle,
                'reference_bon'  => $pret->numero,
                'montant_total'  => 0,
                'statut'         => 'validé',
                'notes'          => "Retour de prêt {$pret->numero} - {$pret->emprunteur_nom}",
            ]);

            EntreeDetail::create([
                'entree_id'     => $entree->id,
                'article_id'    => $article->id,
                'quantite'      => $pret->quantite,
                'prix_unitaire' => 0,
                'montant'       => 0,
            ]);

            $stockAvant = $article->quantite_stock;
            $article->increment('quantite_stock', $pret->quantite);

            MouvementStock::create([
                'article_id'         => $article->id,
                'user_id'            => auth()->id(),
                'type'               => 'entree',
                'quantite'           => $pret->quantite,
                'stock_avant'        => $stockAvant,
                'stock_apres'        => $article->quantite_stock,
                'reference_document' => $entree->numero,
                'motif'              => 'Retour de prêt',
            ]);

            $pret->update([
                'date_retour_reelle' => $request->date_retour_reelle,
                'statut'             => 'rendu',
                'notes'              => $request->notes_retour ? ($pret->notes . "\nRetour : " . $request->notes_retour) : $pret->notes,
            ]);

            ArticleController::verifierAlertes($article);
        });

        return redirect()->route('prets.index')->with('success', 'Retour de prêt enregistré. Stock réintégré.');
    }

    public function annuler(Pret $pret)
    {
        if ($pret->statut !== 'en_cours') {
            return back()->with('error', 'Seuls les prêts en cours peuvent être annulés.');
        }

        DB::transaction(function () use ($pret) {
            $article = $pret->article;
            $stockAvant = $article->quantite_stock;
            $article->increment('quantite_stock', $pret->quantite);

            MouvementStock::create([
                'article_id'         => $article->id,
                'user_id'            => auth()->id(),
                'type'               => 'ajustement',
                'quantite'           => $pret->quantite,
                'stock_avant'        => $stockAvant,
                'stock_apres'        => $article->quantite_stock,
                'reference_document' => $pret->numero,
                'motif'              => 'Annulation de prêt (réintégration)',
            ]);

            $pret->update(['statut' => 'annule']);
            ArticleController::verifierAlertes($article);
        });

        return redirect()->route('prets.index')->with('success', 'Prêt annulé et stock restauré.');
    }

    public function show(Pret $pret)
    {
        $pret->load(['article', 'user']);
        return view('prets.show', compact('pret'));
    }

    public function edit(Pret $pret)
    {
        if ($pret->statut !== 'en_cours') {
            return back()->with('error', 'Seuls les prêts en cours peuvent être modifiés.');
        }
        $articles = Article::where('actif', true)->orderBy('nom')->get();
        return view('prets.edit', compact('pret', 'articles'));
    }

    public function update(Request $request, Pret $pret)
    {
        if ($pret->statut !== 'en_cours') {
            return back()->with('error', 'Seuls les prêts en cours peuvent être modifiés.');
        }

        $request->validate([
            'date_pret'          => 'required|date',
            'date_retour_prevue' => 'nullable|date|after_or_equal:date_pret',
            'emprunteur_nom'     => 'required|string|max:255',
            'emprunteur_telephone' => 'nullable|string|max:20',
            'notes'              => 'nullable|string',
        ]);

        $pret->update($request->only(['date_pret', 'date_retour_prevue', 'emprunteur_nom', 'emprunteur_telephone', 'notes']));

        return redirect()->route('prets.show', $pret)->with('success', 'Prêt modifié avec succès.');
    }

    public function pdf(Pret $pret)
    {
        $pret->load(['article', 'user']);
        $pdf = Pdf::loadView('prets.pdf', compact('pret'));
        return $pdf->download('pret_'.$pret->numero.'.pdf');
    }
}