<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sortie extends Model
{
    protected $fillable = [
        'numero', 'user_id', 'date_sortie', 'client_nom', 'client_telephone',
        'type', 'montant_total', 'montant_recu', 'remise', 'statut', 'notes'
    ];

    protected $casts = [
        'date_sortie' => 'date',
        'montant_total' => 'decimal:2',
        'montant_recu' => 'decimal:2',
        'remise' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(SortieDetail::class);
    }

    public function getMonnaieAttribute(): float
    {
        return max(0, $this->montant_recu - ($this->montant_total - $this->remise));
    }

    public static function genererNumero(): string
    {
        $annee = date('Y');
        $derniere = static::whereYear('created_at', $annee)->orderBy('id', 'desc')->first();
        $num = $derniere ? ($derniere->id + 1) : 1;
        return 'SOR-' . $annee . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
