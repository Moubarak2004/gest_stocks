<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categorie;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nom' => 'Alimentation',        'code' => 'ALIM'],
            ['nom' => 'Boissons',            'code' => 'BOIS'],
            ['nom' => 'Hygiène / Beauté',    'code' => 'HYGI'],
            ['nom' => 'Électronique',        'code' => 'ELEC'],
            ['nom' => 'Vêtements',           'code' => 'VETE'],
            ['nom' => 'Quincaillerie',       'code' => 'QUIN'],
            ['nom' => 'Pharmacie',           'code' => 'PHAR'],
            ['nom' => 'Papeterie',           'code' => 'PAPE'],
            ['nom' => 'Autres',              'code' => 'AUTR'],
        ];

        foreach ($categories as $cat) {
            Categorie::updateOrCreate(['code' => $cat['code']], array_merge($cat, ['actif' => true]));
        }
    }
}
