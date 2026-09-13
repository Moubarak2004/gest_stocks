<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
