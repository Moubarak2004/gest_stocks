<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Entree;
use App\Models\Sortie;
use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RapportController extends Controller
{
    public function index()
    {
        return view('rapports.index');
    }

    public function stockActuel(Request $request)
    {
        $query = Article::with(['categorie', 'fournisseur'])->where('actif', true);

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

        $articles     = $query->orderBy('nom')->get();
        $valeurTotale = $articles->sum(fn($a) => $a->quantite_stock * $a->prix_achat);
        $categories   = \App\Models\Categorie::where('actif', true)->orderBy('nom')->get();

        return view('rapports.stock_actuel', compact('articles', 'valeurTotale', 'categories'));
    }

    public function mouvements(Request $request)
    {
        $debut = $request->date_debut ?? Carbon::now()->startOfMonth()->toDateString();
        $fin   = $request->date_fin   ?? Carbon::now()->toDateString();

        $mouvements = MouvementStock::with(['article', 'user'])
            ->whereBetween('created_at', [$debut . ' 00:00:00', $fin . ' 23:59:59'])
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('rapports.mouvements', compact('mouvements', 'debut', 'fin'));
    }

    public function ventesParPeriode(Request $request)
    {
        $debut = $request->date_debut ?? Carbon::now()->startOfMonth()->toDateString();
        $fin   = $request->date_fin   ?? Carbon::now()->toDateString();

        $sorties = Sortie::with(['details.article', 'user'])
            ->where('statut', 'validé')
            ->whereBetween('date_sortie', [$debut, $fin])
            ->orderByDesc('date_sortie')
            ->get();

        $totalVentes   = $sorties->sum('montant_total');
        $nbTransactions = $sorties->count();

        // Articles les plus vendus
        $topArticles = DB::table('sortie_details')
            ->join('articles', 'articles.id', '=', 'sortie_details.article_id')
            ->join('sorties', 'sorties.id', '=', 'sortie_details.sortie_id')
            ->where('sorties.statut', 'validé')
            ->whereBetween('sorties.date_sortie', [$debut, $fin])
            ->selectRaw('articles.nom, articles.reference, SUM(sortie_details.quantite) as total_vendu, SUM(sortie_details.montant) as chiffre_affaires')
            ->groupBy('articles.id', 'articles.nom', 'articles.reference')
            ->orderByDesc('chiffre_affaires')
            ->limit(10)
            ->get();

        return view('rapports.ventes', compact('sorties', 'totalVentes', 'nbTransactions', 'topArticles', 'debut', 'fin'));
    }

    public function entreesParPeriode(Request $request)
    {
        $debut = $request->date_debut ?? Carbon::now()->startOfMonth()->toDateString();
        $fin   = $request->date_fin   ?? Carbon::now()->toDateString();

        $entrees = Entree::with(['fournisseur', 'user'])
            ->where('statut', 'validé')
            ->whereBetween('date_entree', [$debut, $fin])
            ->orderByDesc('date_entree')
            ->get();

        $totalEntrees = $entrees->sum('montant_total');

        return view('rapports.entrees', compact('entrees', 'totalEntrees', 'debut', 'fin'));
    }
}
