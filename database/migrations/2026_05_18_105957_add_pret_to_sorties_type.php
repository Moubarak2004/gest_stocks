
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // PostgreSQL n'a pas de type ENUM natif modifiable comme MySQL :
            // Laravel matérialise enum() sous forme d'une contrainte CHECK.
            // On la supprime puis on la recrée avec la valeur 'pret' en plus.
            DB::statement("ALTER TABLE sorties DROP CONSTRAINT IF EXISTS sorties_type_check");
            DB::statement("ALTER TABLE sorties ADD CONSTRAINT sorties_type_check CHECK (type IN ('vente','retour_fournisseur','perte','transfert','pret'))");
        } else {
            DB::statement("ALTER TABLE sorties MODIFY COLUMN type ENUM('vente', 'retour_fournisseur', 'perte', 'transfert', 'pret') NOT NULL DEFAULT 'vente'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE sorties DROP CONSTRAINT IF EXISTS sorties_type_check");
            DB::statement("ALTER TABLE sorties ADD CONSTRAINT sorties_type_check CHECK (type IN ('vente','retour_fournisseur','perte','transfert'))");
        } else {
            DB::statement("ALTER TABLE sorties MODIFY COLUMN type ENUM('vente', 'retour_fournisseur', 'perte', 'transfert') NOT NULL DEFAULT 'vente'");
        }
    }
};