<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bons_commande', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('fournisseur_id')->nullable()->constrained('fournisseurs')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->date('date_commande');
            $table->date('date_livraison_prevue')->nullable();
            $table->decimal('montant_total', 15, 2)->default(0);
            $table->enum('statut', ['brouillon', 'envoyé', 'reçu_partiel', 'reçu', 'annulé'])->default('brouillon');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('bon_commande_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bon_commande_id')->constrained('bons_commande')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('articles');
            $table->integer('quantite_commandee');
            $table->integer('quantite_recue')->default(0);
            $table->decimal('prix_unitaire', 15, 2);
            $table->decimal('montant', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bon_commande_details');
        Schema::dropIfExists('bons_commande');
    }
};