<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->enum('type', ['stock_minimum', 'stock_epuise', 'peremption'])->default('stock_minimum');
            $table->integer('quantite_actuelle');
            $table->integer('seuil_alerte');
            $table->boolean('lue')->default(false);
            $table->timestamp('lue_at')->nullable();
            $table->foreignId('lue_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertes');
    }
};
