<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer l'administrateur par défaut
        User::updateOrCreate(
            ['email' => 'admin@stockpro.com'],
            [
                'name'      => 'Administrateur',
                'email'     => 'admin@stockpro.com',
                'password'  => Hash::make('admin123'),
                'role'      => 'admin',
                'actif'     => true,
                'telephone' => '',
            ]
        );

        // Créer le compte gérant
        User::updateOrCreate(
            ['email' => 'mariahgerant@stockpro.com'],
            [
                'name'      => 'Mariah',
                'email'     => 'mariahgerant@stockpro.com',
                'password'  => Hash::make('mariah123'),
                'role'      => 'gerant',
                'actif'     => true,
                'telephone' => '',
            ]
        );

        // Créer un vendeur de test
        User::updateOrCreate(
            ['email' => 'vendeur@stockpro.com'],
            [
                'name'      => 'Vendeur Test',
                'email'     => 'vendeur@stockpro.com',
                'password'  => Hash::make('vendeur123'),
                'role'      => 'vendeur',
                'actif'     => true,
                'telephone' => '',
            ]
        );

        $this->call([
            CategorieSeeder::class,
        ]);
    }
}
