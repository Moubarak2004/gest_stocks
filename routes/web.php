<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\EntreeController;
use App\Http\Controllers\SortieController;
use App\Http\Controllers\BonCommandeController;
use App\Http\Controllers\AlerteController;
use App\Http\Controllers\PretController;
use App\Http\Controllers\RapportController;

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes protégées
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Articles
    Route::get('/articles/search', [ArticleController::class, 'search'])->name('articles.search');
    Route::post('/articles/{article}/ajuster-stock', [ArticleController::class, 'ajusterStock'])->name('articles.ajuster');
    Route::resource('articles', ArticleController::class);
    Route::delete('/articles/{article}/force', [ArticleController::class, 'forceDestroy'])->name('articles.forceDestroy');

    // Catégories (route complète)
    Route::resource('categories', CategorieController::class);

    // Fournisseurs
    Route::resource('fournisseurs', FournisseurController::class);

    // Entrées de stock
    Route::post('/entrees/{entree}/annuler', [EntreeController::class, 'annuler'])->name('entrees.annuler');
    Route::resource('entrees', EntreeController::class)->except(['edit', 'update']);
    Route::get('/entrees/{entree}/edit', [EntreeController::class, 'edit'])->name('entrees.edit');
    Route::put('/entrees/{entree}', [EntreeController::class, 'update'])->name('entrees.update');
    Route::get('/entrees/{entree}/pdf', [EntreeController::class, 'pdf'])->name('entrees.pdf');

    // Sorties de stock
    Route::post('/sorties/{sortie}/annuler', [SortieController::class, 'annuler'])->name('sorties.annuler');
    Route::resource('sorties', SortieController::class)->except(['edit', 'update']);
    Route::get('/sorties/{sortie}/edit', [SortieController::class, 'edit'])->name('sorties.edit');
    Route::put('/sorties/{sortie}', [SortieController::class, 'update'])->name('sorties.update');
    Route::get('/sorties/{sortie}/pdf', [SortieController::class, 'pdf'])->name('sorties.pdf');

    // Bons de commande
    Route::post('/bons/{bon}/receptionner', [BonCommandeController::class, 'receptionner'])->name('bons.receptionner');
    Route::resource('bons', BonCommandeController::class);
    Route::get('/bons/{bon}/pdf', [BonCommandeController::class, 'pdf'])->name('bons.pdf');

    // Alertes
    Route::get('/alertes', [AlerteController::class, 'index'])->name('alertes.index');
    Route::post('/alertes/{alerte}/lue', [AlerteController::class, 'marquerLue'])->name('alertes.lue');
    Route::post('/alertes/toutes-lues', [AlerteController::class, 'marquerToutesLues'])->name('alertes.toutes-lues');
    Route::post('/alertes/generer', [AlerteController::class, 'genererAlertes'])->name('alertes.generer');

    // Rapports
    Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');
    Route::get('/rapports/stock-actuel', [RapportController::class, 'stockActuel'])->name('rapports.stock');
    Route::get('/rapports/mouvements', [RapportController::class, 'mouvements'])->name('rapports.mouvements');
    Route::get('/rapports/ventes', [RapportController::class, 'ventesParPeriode'])->name('rapports.ventes');
    Route::get('/rapports/entrees', [RapportController::class, 'entreesParPeriode'])->name('rapports.entrees');

    // Gestion utilisateurs (admin seulement)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/utilisateurs', [AuthController::class, 'indexUsers'])->name('users.index');
        Route::get('/utilisateurs/creer', [AuthController::class, 'createUser'])->name('users.create');
        Route::post('/utilisateurs', [AuthController::class, 'storeUser'])->name('users.store');
        Route::get('/utilisateurs/{user}/modifier', [AuthController::class, 'editUser'])->name('users.edit');
        Route::put('/utilisateurs/{user}', [AuthController::class, 'updateUser'])->name('users.update');
        Route::delete('/utilisateurs/{user}', [AuthController::class, 'destroyUser'])->name('users.destroy');

        Route::post('/entrees/{entree}/valider', [EntreeController::class, 'valider'])->name('entrees.valider');
Route::post('/sorties/{sortie}/valider', [SortieController::class, 'valider'])->name('sorties.valider');

Route::get('/profil', [AuthController::class, 'profile'])->name('profile');
Route::put('/profil/mot-de-passe', [AuthController::class, 'updatePassword'])->name('profile.update-password');

        // Prêts (accessible aussi aux gérants)
        Route::middleware(['role:admin,gerant'])->group(function () {
            Route::resource('prets', PretController::class)->except(['edit', 'update']);
            Route::get('prets/{pret}/retour', [PretController::class, 'retourForm'])->name('prets.retour');
            Route::post('prets/{pret}/retourner', [PretController::class, 'retourner'])->name('prets.retourner');
            Route::post('prets/{pret}/annuler', [PretController::class, 'annuler'])->name('prets.annuler');
            Route::get('/prets/{pret}/edit', [PretController::class, 'edit'])->name('prets.edit');
            Route::put('/prets/{pret}', [PretController::class, 'update'])->name('prets.update');
            Route::get('/prets/{pret}/pdf', [PretController::class, 'pdf'])->name('prets.pdf');
        });
    });
});