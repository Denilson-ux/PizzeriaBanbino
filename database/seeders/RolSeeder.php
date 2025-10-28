<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
   
    public function run(): void
    {
        DB::table('rol')->insert([
            'nombre' => 'Administrador',
        ]);

        DB::table('rol')->insert([
            'nombre' => 'Cajer@',
        ]);

        DB::table('rol')->insert([
            'nombre' => 'Repartidor',
        ]);

        DB::table('rol')->insert([
            'nombre' => 'Cliente',
        ]);
    }
}