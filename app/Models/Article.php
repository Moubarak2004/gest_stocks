<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'reference', 'code_barre', 'categorie_id', 'fournisseur_id',
        'description', 'unite', 'prix_achat', 'prix_vente', 'prix_vente_detail',
        'quantite_stock', 'stock_minimum', 'stock_maximum', 'emplacement',
        'image', 'actif', 'notes',
    ];

    protected $casts = [
        'prix_achat' => 'decimal:2',
        'prix_vente' => 'decimal:2',
        'prix_vente_detail' => 'decimal:2',
        'actif' => 'boolean',
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function entreeDetails()
    {
        return $this->hasMany(EntreeDetail::class);
    }

    public function sortieDetails()
    {
        return $this->hasMany(SortieDetail::class);
    }

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }

    public function alertes()
    {
        return $this->hasMany(Alerte::class);
    }

    public function estEnRuptureDeStock(): bool
    {
        return $this->quantite_stock <= 0;
    }

    public function estEnAlerte(): bool
    {
        return $this->quantite_stock > 0 && $this->quantite_stock <= $this->stock_minimum;
    }

    public function getStatutStockAttribute(): string
    {
        if ($this->estEnRuptureDeStock()) return 'rupture';
        if ($this->estEnAlerte()) return 'alerte';
        return 'normal';
    }

    public function getValeurStockAttribute(): float
    {
        return $this->quantite_stock * $this->prix_achat;
    }

    // Générer référence automatique
    public static function genererReference(): string
    {
        $dernier = static::orderBy('id', 'desc')->first();
        $num = $dernier ? ($dernier->id + 1) : 1;
        return 'ART-' . str_pad($num, 5, '0', STR_PAD_LEFT);
    }
}
