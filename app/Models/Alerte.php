<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerte extends Model
{
    protected $fillable = [
        'article_id', 'type', 'quantite_actuelle', 'seuil_alerte', 'lue', 'lue_at', 'lue_par'
    ];

    protected $casts = ['lue' => 'boolean', 'lue_at' => 'datetime'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function luePar()
    {
        return $this->belongsTo(User::class, 'lue_par');
    }
}
