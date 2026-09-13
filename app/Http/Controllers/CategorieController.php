<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    /**
     * Afficher la liste des catégories.
     */
  /*  public function index()
    {
        $categories = Categorie::withCount('articles')->orderBy('nom')->paginate(20);
        return view('categories.index', compact('categories'));
    }  */
         public function index(Request $request)
    {
        $search = $request->input('search');

        $categories = Categorie::withCount('articles')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('nom')
            ->paginate(20)
            ->withQueryString(); // conserve le paramètre search dans les liens de pagination

        return view('categories.index', compact('categories', 'search'));
    }

    /**
     * Afficher le formulaire de création d'une catégorie.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Enregistrer une nouvelle catégorie dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom'  => 'required|string|max:255|unique:categories,nom',
            'code' => 'nullable|string|unique:categories,code',
        ], [
            'nom.required' => 'Le nom de la catégorie est obligatoire.',
            'nom.unique'   => 'Ce nom de catégorie existe déjà.',
            'code.unique'  => 'Ce code de catégorie est déjà utilisé.',
        ]);

        Categorie::create([
            'nom'         => $request->nom,
            'code'        => $request->code,
            'description' => $request->description,
            'actif'       => true, // Actif par défaut à la création
        ]);

        return redirect()->route('categories.index')->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Afficher la catégorie spécifique.
     */
    public function show($id)
    {
        $categorie = Categorie::findOrFail($id);
        return view('categories.show', compact('categorie'));
    }

    /**
     * Afficher le formulaire d'édition d'une catégorie.
     */
    public function edit($id)
    {
        $categorie = Categorie::findOrFail($id);
        return view('categories.edit', compact('categorie'));
    }

    /**
     * Mettre à jour la catégorie spécifiée dans la base de données.
     */
    public function update(Request $request, $id)
    {
        $categorie = Categorie::findOrFail($id);

        $request->validate([
            'nom'  => 'required|string|max:255|unique:categories,nom,' . $categorie->id,
            'code' => 'nullable|string|unique:categories,code,' . $categorie->id,
        ], [
            'nom.required' => 'Le nom de la catégorie est obligatoire.',
            'nom.unique'   => 'Ce nom de catégorie est déjà associé à une autre catégorie.',
            'code.unique'  => 'Ce code est déjà associé à une autre catégorie.',
        ]);

        // Préparation des données à mettre à jour
        $data = [
            'nom'         => $request->nom,
            'code'        => $request->code,
            'description' => $request->description,
            'actif'       => $request->has('actif') ? true : false,
        ];

        $categorie->update($data);

        return redirect()->route('categories.index')->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Supprimer la catégorie de la base de données si elle est vide.
     */
    public function destroy($id)
    {
        $categorie = Categorie::findOrFail($id);

        if ($categorie->articles()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer : cette catégorie contient des articles.');
        }

        $categorie->delete();

        return redirect()->route('categories.index')->with('success', 'Catégorie supprimée avec succès.');
    }
}