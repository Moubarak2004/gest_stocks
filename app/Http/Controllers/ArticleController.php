<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Categorie;
use App\Models\Fournisseur;
use App\Models\Alerte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['categorie', 'fournisseur'])->where('actif', true);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($w) use ($q) {
                $w->where('nom', 'like', "%$q%")
                  ->orWhere('reference', 'like', "%$q%")
                  ->orWhere('code_barre', 'like', "%$q%");
            });
        }

        if ($request->filled('categorie')) {
            $query->where('categorie_id', $request->categorie);
        }

        if ($request->filled('statut')) {
            match($request->statut) {
                'alerte'  => $query->whereRaw('quantite_stock <= stock_minimum AND quantite_stock > 0'),
                'rupture' => $query->where('quantite_stock', '<=', 0),
                'normal'  => $query->whereRaw('quantite_stock > stock_minimum'),
                default   => null,
            };
        }

        $articles    = $query->orderBy('nom')->paginate(20)->withQueryString();
        $categories  = Categorie::where('actif', true)->orderBy('nom')->get();

        return view('articles.index', compact('articles', 'categories'));
    }

    public function create()
    {
        $categories  = Categorie::where('actif', true)->orderBy('nom')->get();
        $fournisseurs = Fournisseur::where('actif', true)->orderBy('nom')->get();
        $reference   = Article::genererReference();
        return view('articles.create', compact('categories', 'fournisseurs', 'reference'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'              => 'required|string|max:255',
            'reference'        => 'required|string|unique:articles,reference',
            'prix_achat'       => 'required|numeric|min:0',
            'prix_vente'       => 'required|numeric|min:0',
            'stock_minimum'    => 'required|integer|min:0',
            'categorie_id'     => 'nullable|exists:categories,id',
            'fournisseur_id'   => 'nullable|exists:fournisseurs,id',
            'image'            => 'nullable|image|max:2048',
        ]);

        $data = $request->except('image');
        $data['prix_vente_detail'] = $request->prix_vente_detail ?? $request->prix_vente;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        Article::create($data);
        return redirect()->route('articles.index')->with('success', 'Article créé avec succès.');
    }

    public function show(Article $article)
    {
        $article->load(['categorie', 'fournisseur', 'mouvements.user']);
        $mouvements = $article->mouvements()->with('user')->orderByDesc('created_at')->paginate(15);
        return view('articles.show', compact('article', 'mouvements'));
    }

    public function edit(Article $article)
    {
        $categories   = Categorie::where('actif', true)->orderBy('nom')->get();
        $fournisseurs = Fournisseur::where('actif', true)->orderBy('nom')->get();
        return view('articles.edit', compact('article', 'categories', 'fournisseurs'));
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'nom'            => 'required|string|max:255',
            'reference'      => 'required|string|unique:articles,reference,' . $article->id,
            'prix_achat'     => 'required|numeric|min:0',
            'prix_vente'     => 'required|numeric|min:0',
            'stock_minimum'  => 'required|integer|min:0',
            'categorie_id'   => 'nullable|exists:categories,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'image'          => 'nullable|image|max:2048',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($article->image) Storage::disk('public')->delete($article->image);
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($data);
        return redirect()->route('articles.index')->with('success', 'Article modifié avec succès.');
    }

    public function destroy(Article $article)
    {
        if ($article->image) Storage::disk('public')->delete($article->image);
        $article->update(['actif' => false]);
        return redirect()->route('articles.index')->with('success', 'Article archivé avec succès.');
    }

    // Ajustement de stock
    public function ajusterStock(Request $request, Article $article)
    {
        $request->validate([
            'quantite' => 'required|integer',
            'motif'    => 'required|string|max:255',
        ]);

        $stockAvant = $article->quantite_stock;
        $article->increment('quantite_stock', $request->quantite);

        \App\Models\MouvementStock::create([
            'article_id'         => $article->id,
            'user_id'            => auth()->id(),
            'type'               => 'ajustement',
            'quantite'           => $request->quantite,
            'stock_avant'        => $stockAvant,
            'stock_apres'        => $article->quantite_stock,
            'motif'              => $request->motif,
        ]);

        $this->verifierAlertes($article);

        return back()->with('success', 'Stock ajusté avec succès.');
    }

    public static function verifierAlertes(Article $article): void
    {
        if ($article->quantite_stock <= $article->stock_minimum) {
            $type = $article->quantite_stock <= 0 ? 'stock_epuise' : 'stock_minimum';

            // Créer alerte si pas déjà existante
            Alerte::firstOrCreate(
                ['article_id' => $article->id, 'type' => $type, 'lue' => false],
                [
                    'quantite_actuelle' => $article->quantite_stock,
                    'seuil_alerte'      => $article->stock_minimum,
                ]
            );
        }
    }

    public function search(Request $request)
    {
        $articles = Article::where('actif', true)
            ->where(function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->q . '%')
                  ->orWhere('reference', 'like', '%' . $request->q . '%');
            })
            ->where('quantite_stock', '>', 0)
            ->limit(10)
            ->get(['id', 'nom', 'reference', 'prix_vente', 'prix_vente_detail', 'quantite_stock', 'unite']);

        return response()->json($articles);
    }

    public function forceDestroy(Article $article)
{
    if ($article->image) Storage::disk('public')->delete($article->image);
    $article->delete(); // suppression réelle
    return redirect()->route('articles.index')->with('success', 'Article supprimé définitivement.');
}
}