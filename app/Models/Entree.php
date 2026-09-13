<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entree extends Model
{
    protected $fillable = [
        'numero', 'fournisseur_id', 'user_id', 'date_entree',
        'reference_bon', 'montant_total', 'statut', 'notes'
    ];

    protected $casts = ['date_entree' => 'date', 'montant_total' => 'decimal:2'];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(EntreeDetail::class);
    }

    public static function genererNumero(): string
    {
        $annee = date('Y');
        $derniere = static::whereYear('created_at', $annee)->orderBy('id', 'desc')->first();
        $num = $derniere ? ($derniere->id + 1) : 1;
        return 'ENT-' . $annee . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}

class EntreeDetail extends Model
{
    protected $fillable = ['entree_id', 'article_id', 'quantite', 'prix_unitaire', 'montant'];
    protected $casts = ['prix_unitaire' => 'decimal:2', 'montant' => 'decimal:2'];

    public function entree()
    {
        return $this->belongsTo(Entree::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
