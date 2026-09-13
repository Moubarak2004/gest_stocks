<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('reference')->unique();
            $table->string('code_barre')->nullable()->unique();
            $table->foreignId('categorie_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('fournisseur_id')->nullable()->constrained('fournisseurs')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('unite')->default('Pièce'); // Pièce, Kg, Litre, Carton...
            $table->decimal('prix_achat', 15, 2)->default(0);
            $table->decimal('prix_vente', 15, 2)->default(0);
            $table->decimal('prix_vente_detail', 15, 2)->default(0);
            $table->integer('quantite_stock')->default(0);
            $table->integer('stock_minimum')->default(5); // seuil d'alerte
            $table->integer('stock_maximum')->default(1000);
            $table->string('emplacement')->nullable(); // localisation dans le magasin
            $table->string('image')->nullable();
            $table->boolean('actif')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
