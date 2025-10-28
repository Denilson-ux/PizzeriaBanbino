<?php

namespace Database\Seeders;

use App\Models\TipoPago;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoPago::create([
            'nombre' => 'Efectivo',
            'descripcion' => 'Pagos en efectivo, de manera presencial',
        ]);

        TipoPago::create([
            'nombre' => 'Paypal',
            'descripcion' => 'Pagos realizados por paypal',
        ]);

        TipoPago::create([
            'nombre' => 'Tarjeta',
            'descripcion' => 'Pagos realizados con tarjeta de débito o crédito',
        ]);
    }
}
