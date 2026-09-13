<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('type', ['entree', 'sortie', 'ajustement', 'inventaire']);
            $table->integer('quantite'); // positif=entrée, négatif=sortie
            $table->integer('stock_avant');
            $table->integer('stock_apres');
            $table->string('reference_document')->nullable(); // numéro entrée/sortie/bon
            $table->string('motif')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
