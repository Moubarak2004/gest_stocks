<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    protected $fillable = [
        'nom', 'code', 'email', 'telephone', 'adresse', 'ville', 'pays', 'solde', 'actif', 'notes'
    ];
    protected $casts = ['actif' => 'boolean', 'solde' => 'decimal:2'];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function entrees()
    {
        return $this->hasMany(Entree::class);
    }

    public function bonsCommande()
    {
        return $this->hasMany(BonCommande::class);
    }

    public static function genererCode(): string
    {
        $dernier = static::orderBy('id', 'desc')->first();
        $num = $dernier ? ($dernier->id + 1) : 1;
        return 'FOUR-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
