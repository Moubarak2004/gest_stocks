<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Entree;
use App\Models\Sortie;
use App\Models\Alerte;
use App\Models\BonCommande;
use App\Models\MouvementStock;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques générales
        $totalArticles   = Article::where('actif', true)->count();
        $articlesAlerte  = Article::where('actif', true)->whereRaw('quantite_stock <= stock_minimum AND quantite_stock > 0')->count();
        $articlesRupture = Article::where('actif', true)->where('quantite_stock', '<=', 0)->count();
        $valeurStock     = Article::where('actif', true)->selectRaw('SUM(quantite_stock * prix_achat) as total')->value('total') ?? 0;

        // Ce mois
        $moisCourant = Carbon::now()->startOfMonth();
        $entreesduMois = Entree::where('statut', 'validé')->where('date_entree', '>=', $moisCourant)->sum('montant_total');
        $sortiesDuMois = Sortie::where('statut', 'validé')->where('date_sortie', '>=', $moisCourant)->sum('montant_total');

        // Alertes non lues
        $alertesNonLues = Alerte::where('lue', false)->count();

        // Bons en attente
        $bonsEnAttente = BonCommande::whereIn('statut', ['brouillon', 'envoyé'])->count();

        // Articles les plus vendus ce mois
        $topArticles = DB::table('sortie_details')
            ->join('articles', 'articles.id', '=', 'sortie_details.article_id')
            ->join('sorties', 'sorties.id', '=', 'sortie_details.sortie_id')
            ->where('sorties.statut', 'validé')
            ->where('sorties.date_sortie', '>=', $moisCourant)
            ->selectRaw('articles.nom, articles.reference, SUM(sortie_details.quantite) as total_vendu, SUM(sortie_details.montant) as chiffre_affaires')
            ->groupBy('articles.id', 'articles.nom', 'articles.reference')
            ->orderByDesc('total_vendu')
            ->limit(5)
            ->get();

        // Derniers mouvements
        $derniersMouvements = MouvementStock::with(['article', 'user'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Graphique entrées/sorties sur 6 mois
        $graphData = $this->getGraphData();

        // Articles en alerte
        $alertesArticles = Article::where('actif', true)
            ->whereRaw('quantite_stock <= stock_minimum')
            ->with('categorie')
            ->orderBy('quantite_stock')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'totalArticles', 'articlesAlerte', 'articlesRupture', 'valeurStock',
            'entreesduMois', 'sortiesDuMois', 'alertesNonLues', 'bonsEnAttente',
            'topArticles', 'derniersMouvements', 'graphData', 'alertesArticles'
        ));
    }

    private function getGraphData(): array
    {
        $mois   = [];
        $entrees = [];
        $sorties = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $mois[] = $date->format('M Y');

            $entrees[] = Entree::where('statut', 'validé')
                ->whereYear('date_entree', $date->year)
                ->whereMonth('date_entree', $date->month)
                ->sum('montant_total');

            $sorties[] = Sortie::where('statut', 'validé')
                ->whereYear('date_sortie', $date->year)
                ->whereMonth('date_sortie', $date->month)
                ->sum('montant_total');
        }

        return compact('mois', 'entrees', 'sorties');
    }
}
