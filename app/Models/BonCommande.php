<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonCommande extends Model
{
    protected $table = 'bons_commande';
    protected $fillable = [
        'numero', 'fournisseur_id', 'user_id', 'date_commande',
        'date_livraison_prevue', 'montant_total', 'statut', 'notes'
    ];

    protected $casts = [
        'date_commande' => 'date',
        'date_livraison_prevue' => 'date',
        'montant_total' => 'decimal:2',
    ];

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
        return $this->hasMany(BonCommandeDetail::class);
    }

    public static function genererNumero(): string
    {
        $annee = date('Y');
        $dernier = static::whereYear('created_at', $annee)->orderBy('id', 'desc')->first();
        $num = $dernier ? ($dernier->id + 1) : 1;
        return 'BC-' . $annee . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}