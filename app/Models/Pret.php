<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pret extends Model
{
    protected $fillable = [
        'numero', 'article_id', 'quantite', 'emprunteur_nom', 'emprunteur_telephone',
        'date_pret', 'date_retour_prevue', 'date_retour_reelle', 'statut', 'notes',
        'user_id', 'reference_sortie'
    ];

    protected $casts = [
        'date_pret'         => 'date',
        'date_retour_prevue'=> 'date',
        'date_retour_reelle'=> 'date',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Générer un numéro unique pour le prêt
    public static function genererNumero(): string
    {
        $annee = date('Y');
        $dernier = static::whereYear('created_at', $annee)->orderBy('id', 'desc')->first();
        $num = $dernier ? ($dernier->id + 1) : 1;
        return 'PRET-' . $annee . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    // Vérifier si le prêt est en retard
    public function isEnRetard(): bool
    {
        return $this->statut === 'en_cours' && $this->date_retour_prevue && $this->date_retour_prevue < now();
    }
}
