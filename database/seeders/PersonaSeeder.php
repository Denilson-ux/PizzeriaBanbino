<?php

namespace Database\Seeders;

use App\Models\Persona;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Para usuario
        Persona::create([
            'ci' => '123456789',
            'nombre' => 'Juan',
            'paterno' => 'Pérez',
            'materno' => 'Gómez',
            'telefono' => '555-1234',
            'direccion' => 'Calle 123',
            'correo' => 'juan@example.com',
            'imagen' => 'avatar.jpg',
            'estado' => 1,
        ]);

        //para cliente s/n
        /*
        Persona::create([
            'ci' => 'S/CI',
            'nombre' => 'S/N',
            'paterno' => 'S/P',
            'materno' => 'S/M',
            'telefono' => 'S/T',
            'direccion' => 'S/T',
            'correo' => 'sn@example.com',
            'imagen' => 'avatarsn.jpg',
            'estado' => 1,
        ]);
        */
    }
}
