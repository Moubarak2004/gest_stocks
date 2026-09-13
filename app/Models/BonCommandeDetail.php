<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonCommandeDetail extends Model
{
    protected $fillable = [
        'bon_commande_id', 'article_id', 'quantite_commandee', 'quantite_recue', 'prix_unitaire', 'montant'
    ];
    protected $casts = ['prix_unitaire' => 'decimal:2', 'montant' => 'decimal:2'];

    public function bonCommande()
    {
        return $this->belongsTo(BonCommande::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
