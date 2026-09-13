<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prets', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();                 // Numéro de prêt (PRET-2026-0001)
            $table->foreignId('article_id')->constrained()->onDelete('restrict');
            $table->integer('quantite');                        // Quantité prêtée
            $table->string('emprunteur_nom');                   // Nom de la personne/entité
            $table->string('emprunteur_telephone')->nullable(); // Téléphone
            $table->date('date_pret');                          // Date du prêt
            $table->date('date_retour_prevue')->nullable();     // Date prévue de retour
            $table->date('date_retour_reelle')->nullable();     // Date réelle de retour
            $table->enum('statut', ['en_cours', 'retard', 'rendu', 'annule'])->default('en_cours');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained();        // Qui a enregistré le prêt
            $table->string('reference_sortie')->nullable();     // Lien vers la sortie associée (type='pret')
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prets');
    }
};
