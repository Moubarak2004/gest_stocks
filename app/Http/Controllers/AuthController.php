<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required'    => 'L\'email est obligatoire.',
            'email.email'       => 'L\'email n\'est pas valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min'      => 'Le mot de passe doit faire au moins 6 caractères.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            if (!Auth::user()->actif) {
                Auth::logout();
                return back()->withErrors(['email' => 'Votre compte est désactivé. Contactez l\'administrateur.']);
            }
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Bienvenue ' . Auth::user()->name . ' !');
        }

        return back()->withErrors([
            'email' => 'Email ou mot de passe incorrect.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Vous avez été déconnecté avec succès.');
    }

    // Gestion des utilisateurs (admin only)
    public function indexUsers()
    {
        $users = User::orderBy('name')->get();
        return view('auth.users', compact('users'));
    }

    public function createUser()
    {
        return view('auth.create_user');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6|confirmed',
            'role'      => 'required|in:admin,gerant,vendeur',
            'telephone' => 'nullable|string|max:20',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'telephone' => $request->telephone,
            'actif'     => true,
        ]);

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function editUser(User $user)
    {
        return view('auth.edit_user', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'role'      => 'required|in:admin,gerant,vendeur',
            'telephone' => 'nullable|string|max:20',
            'password'  => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'role'      => $request->role,
            'telephone' => $request->telephone,
            'actif'     => $request->boolean('actif'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé.');
    }
  /*  public function profile()
{
    $user = auth()->user();
    return view('auth.profile', compact('user'));
}  */
    public function profile()
{
    $user = auth()->user();
    return view('auth.profile', compact('user'));
}

public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => ['required', 'current_password'],
        'password' => ['required', 'confirmed', 'min:6'],
    ]);

    $user = auth()->user();
    $user->password = Hash::make($request->password);
    $user->save();

    return back()->with('success', 'Mot de passe mis à jour avec succès.');
}
}