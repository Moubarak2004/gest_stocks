<?php
// app/Models/Categorie.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    protected $fillable = ['nom', 'code', 'description', 'actif'];
    protected $casts = ['actif' => 'boolean'];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
