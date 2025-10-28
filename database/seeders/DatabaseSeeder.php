<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(RolesSeeder::class);
        $this->call(RolSeeder::class); //Porque y no da tiempo de camibarlo ;v
        $this->call(PersonaSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(EmpleadoSeeder::class);
        $this->call(ClienteSeeder::class);
        $this->call(TipoMenuSeeder::class);
        $this->call(ItemMenuSeeder::class);
        $this->call(TipoPagoSeeder::class);
        $this->call(IngredientesSeeder::class); // Agregar ingredientes de ejemplo

        /*php artisan db:seed --class=PersonaSeeder */

    }
}