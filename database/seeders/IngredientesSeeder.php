<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ingrediente;
use App\Models\Almacen;

class IngredientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredientes = [
            // Lácteos
            [
                'nombre' => 'Queso Mozzarella',
                'descripcion' => 'Queso mozzarella para pizza',
                'unidad_medida' => 'kilogramos',
                'categoria' => 'lacteos',
                'es_perecedero' => true,
                'dias_vencimiento' => 15,
                'estado' => 'activo',
                'stock_inicial' => 50,
                'stock_minimo' => 10,
                'stock_maximo' => 100,
                'costo_unitario' => 18.50
            ],
            [
                'nombre' => 'Queso Parmesano',
                'descripcion' => 'Queso parmesano rallado',
                'unidad_medida' => 'kilogramos',
                'categoria' => 'lacteos',
                'es_perecedero' => true,
                'dias_vencimiento' => 30,
                'estado' => 'activo',
                'stock_inicial' => 20,
                'stock_minimo' => 5,
                'stock_maximo' => 50,
                'costo_unitario' => 25.00
            ],
            
            // Carnes
            [
                'nombre' => 'Pepperoni',
                'descripcion' => 'Pepperoni en rodajas para pizza',
                'unidad_medida' => 'kilogramos',
                'categoria' => 'carnes',
                'es_perecedero' => true,
                'dias_vencimiento' => 7,
                'estado' => 'activo',
                'stock_inicial' => 30,
                'stock_minimo' => 8,
                'stock_maximo' => 60,
                'costo_unitario' => 22.00
            ],
            [
                'nombre' => 'Jamón',
                'descripcion' => 'Jamón en cubos para pizza',
                'unidad_medida' => 'kilogramos',
                'categoria' => 'carnes',
                'es_perecedero' => true,
                'dias_vencimiento' => 5,
                'estado' => 'activo',
                'stock_inicial' => 25,
                'stock_minimo' => 6,
                'stock_maximo' => 50,
                'costo_unitario' => 20.00
            ],
            
            // Vegetales
            [
                'nombre' => 'Tomate',
                'descripcion' => 'Tomate fresco para pizza',
                'unidad_medida' => 'kilogramos',
                'categoria' => 'vegetales',
                'es_perecedero' => true,
                'dias_vencimiento' => 3,
                'estado' => 'activo',
                'stock_inicial' => 40,
                'stock_minimo' => 10,
                'stock_maximo' => 80,
                'costo_unitario' => 4.50
            ],
            [
                'nombre' => 'Cebolla',
                'descripcion' => 'Cebolla roja en rodajas',
                'unidad_medida' => 'kilogramos',
                'categoria' => 'vegetales',
                'es_perecedero' => true,
                'dias_vencimiento' => 7,
                'estado' => 'activo',
                'stock_inicial' => 30,
                'stock_minimo' => 8,
                'stock_maximo' => 60,
                'costo_unitario' => 3.20
            ],
            
            // Harinas
            [
                'nombre' => 'Harina de Trigo',
                'descripcion' => 'Harina de trigo para masa de pizza',
                'unidad_medida' => 'kilogramos',
                'categoria' => 'harinas',
                'es_perecedero' => false,
                'estado' => 'activo',
                'stock_inicial' => 200,
                'stock_minimo' => 50,
                'stock_maximo' => 400,
                'costo_unitario' => 2.80
            ],
            
            // Condimentos
            [
                'nombre' => 'Sal',
                'descripcion' => 'Sal de mesa',
                'unidad_medida' => 'kilogramos',
                'categoria' => 'condimentos',
                'es_perecedero' => false,
                'estado' => 'activo',
                'stock_inicial' => 10,
                'stock_minimo' => 2,
                'stock_maximo' => 20,
                'costo_unitario' => 1.50
            ],
            [
                'nombre' => 'Orégano',
                'descripcion' => 'Orégano seco',
                'unidad_medida' => 'gramos',
                'categoria' => 'condimentos',
                'es_perecedero' => false,
                'estado' => 'activo',
                'stock_inicial' => 500,
                'stock_minimo' => 100,
                'stock_maximo' => 1000,
                'costo_unitario' => 0.02
            ],
            
            // Bebidas
            [
                'nombre' => 'Aceite de Oliva',
                'descripcion' => 'Aceite de oliva extra virgen',
                'unidad_medida' => 'litros',
                'categoria' => 'otros',
                'es_perecedero' => false,
                'estado' => 'activo',
                'stock_inicial' => 15,
                'stock_minimo' => 3,
                'stock_maximo' => 30,
                'costo_unitario' => 12.00
            ]
        ];
        
        foreach ($ingredientes as $data) {
            // Separar datos de ingrediente y almacén
            $ingredienteData = [
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'],
                'unidad_medida' => $data['unidad_medida'],
                'categoria' => $data['categoria'],
                'es_perecedero' => $data['es_perecedero'],
                'dias_vencimiento' => $data['dias_vencimiento'] ?? null,
                'estado' => $data['estado']
            ];
            
            $almacenData = [
                'stock_actual' => $data['stock_inicial'],
                'stock_minimo' => $data['stock_minimo'],
                'stock_maximo' => $data['stock_maximo'],
                'unidad_medida' => $data['unidad_medida'],
                'costo_unitario_promedio' => $data['costo_unitario'],
                'fecha_ultimo_ingreso' => now(),
                'estado' => 'activo'
            ];
            
            // Crear ingrediente
            $ingrediente = Ingrediente::create($ingredienteData);
            
            // Crear registro de almacén
            $ingrediente->almacen()->create($almacenData);
        }
        
        $this->command->info('Se han creado ' . count($ingredientes) . ' ingredientes con sus registros de almacén.');
    }
}