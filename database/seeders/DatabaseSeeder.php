<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Création du compte Admin
        User::create([
            'name' => 'Administrateur ENSA',
            'email' => 'admin@ensa.local',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2. Création du compte Utilisateur de test
        User::create([
            'name' => 'Etudiant Test',
            'email' => 'user@ensa.local',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        // 3. Création de quelques catégories pour la boutique
        Category::create(['name' => 'Électronique', 'description' => 'PC, Téléphones, etc.']);
        Category::create(['name' => 'Livres', 'description' => 'Supports de cours et romans']);
        Category::create(['name' => 'Accessoires', 'description' => 'Claviers, souris, casques']);
        
        $this->command->info('Base de données remplie avec succès !');
    }
}