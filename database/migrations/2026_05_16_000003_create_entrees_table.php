<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrees', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique(); // Numéro d'entrée
            $table->foreignId('fournisseur_id')->nullable()->constrained('fournisseurs')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users'); // qui a fait l'entrée
            $table->date('date_entree');
            $table->string('reference_bon')->nullable(); // Bon de livraison fournisseur
            $table->decimal('montant_total', 15, 2)->default(0);
            $table->enum('statut', ['brouillon', 'validé', 'annulé'])->default('validé');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('entree_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entree_id')->constrained('entrees')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('articles');
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 15, 2);
            $table->decimal('montant', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entree_details');
        Schema::dropIfExists('entrees');
    }
};
