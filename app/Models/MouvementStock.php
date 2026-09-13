<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    protected $table = 'mouvements_stock';
    protected $fillable = [
        'article_id', 'user_id', 'type', 'quantite',
        'stock_avant', 'stock_apres', 'reference_document', 'motif'
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}