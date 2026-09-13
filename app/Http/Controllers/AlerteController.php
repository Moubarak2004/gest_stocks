<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\Article;
use Illuminate\Http\Request;

class AlerteController extends Controller
{
    public function index()
    {
        $alertes = Alerte::with('article.categorie')
            ->orderBy('lue')
            ->orderByDesc('created_at')
            ->paginate(30);

        $articlesRupture = Article::where('actif', true)->where('quantite_stock', '<=', 0)->get();
        $articlesAlerte  = Article::where('actif', true)
            ->whereRaw('quantite_stock > 0 AND quantite_stock <= stock_minimum')
            ->get();

        return view('alertes.index', compact('alertes', 'articlesRupture', 'articlesAlerte'));
    }

    public function marquerLue(Alerte $alerte)
    {
        $alerte->update(['lue' => true, 'lue_at' => now(), 'lue_par' => auth()->id()]);
        return back()->with('success', 'Alerte marquée comme lue.');
    }

    public function marquerToutesLues()
    {
        Alerte::where('lue', false)->update([
            'lue'    => true,
            'lue_at' => now(),
            'lue_par'=> auth()->id(),
        ]);
        return back()->with('success', 'Toutes les alertes ont été marquées comme lues.');
    }

    // Générer alertes pour tous les articles sous le seuil
    public function genererAlertes()
    {
        $articles = Article::where('actif', true)
            ->whereRaw('quantite_stock <= stock_minimum')
            ->get();

        $count = 0;
        foreach ($articles as $article) {
            ArticleController::verifierAlertes($article);
            $count++;
        }

        return back()->with('success', "$count alerte(s) générée(s).");
    }
}
