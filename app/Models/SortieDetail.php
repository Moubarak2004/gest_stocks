<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SortieDetail extends Model
{
    protected $fillable = ['sortie_id', 'article_id', 'quantite', 'prix_unitaire', 'montant'];
    protected $casts = ['prix_unitaire' => 'decimal:2', 'montant' => 'decimal:2'];

    public function sortie()
    {
        return $this->belongsTo(Sortie::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
