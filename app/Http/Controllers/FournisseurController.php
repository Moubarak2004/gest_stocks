<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    public function index(Request $request)
    {
        $query = Fournisseur::withCount('articles');
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($w) use ($q) {
                $w->where('nom', 'like', "%$q%")->orWhere('telephone', 'like', "%$q%");
            });
        }
        $fournisseurs = $query->orderBy('nom')->paginate(20)->withQueryString();
        return view('fournisseurs.index', compact('fournisseurs'));
    }

    public function create()
    {
        $code = Fournisseur::genererCode();
        return view('fournisseurs.create', compact('code'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'       => 'required|string|max:255',
            'email'     => 'nullable|email|unique:fournisseurs,email',
            'telephone' => 'nullable|string|max:20',
        ]);

        $data         = $request->all();
        $data['code'] = $data['code'] ?? Fournisseur::genererCode();
        Fournisseur::create($data);

        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur créé avec succès.');
    }

    public function show(Fournisseur $fournisseur)
    {
        $fournisseur->load('articles');
        $entrees = $fournisseur->entrees()->with('user')->orderByDesc('date_entree')->paginate(10);
        return view('fournisseurs.show', compact('fournisseur', 'entrees'));
    }

    public function edit(Fournisseur $fournisseur)
    {
        return view('fournisseurs.edit', compact('fournisseur'));
    }

    public function update(Request $request, Fournisseur $fournisseur)
    {
        $request->validate([
            'nom'       => 'required|string|max:255',
            'email'     => 'nullable|email|unique:fournisseurs,email,' . $fournisseur->id,
            'telephone' => 'nullable|string|max:20',
        ]);
        $fournisseur->update($request->all());
        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur mis à jour.');
    }

    public function destroy(Fournisseur $fournisseur)
    {
        if ($fournisseur->entrees()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer : ce fournisseur a des entrées associées.');
        }
        $fournisseur->delete();
        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur supprimé.');
    }
}
