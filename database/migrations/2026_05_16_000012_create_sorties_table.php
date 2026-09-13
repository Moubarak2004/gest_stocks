<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sorties', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->date('date_sortie');
            $table->string('client_nom')->nullable();
            $table->string('client_telephone')->nullable();
            $table->enum('type', ['vente', 'retour_fournisseur', 'perte', 'transfert'])->default('vente');
            $table->decimal('montant_total', 15, 2)->default(0);
            $table->decimal('montant_recu', 15, 2)->default(0);
            $table->decimal('remise', 15, 2)->default(0);
            $table->enum('statut', ['brouillon', 'validé', 'annulé'])->default('validé');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sortie_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sortie_id')->constrained('sorties')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('articles');
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 15, 2);
            $table->decimal('montant', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sortie_details');
        Schema::dropIfExists('sorties');
    }
};
