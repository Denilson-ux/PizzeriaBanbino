<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ingrediente;
use Illuminate\Support\Facades\DB;

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
        
        // Obtener el ID del almacén principal - probar primero 'almacenes_fisicos' y fallback a 'almacenes'
        $almacenPrincipalId = DB::table('information_schema.tables')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->whereIn('TABLE_NAME', ['almacenes_fisicos', 'almacenes'])
            ->orderByRaw("FIELD(TABLE_NAME,'almacenes_fisicos','almacenes')")
            ->first();

        if (!$almacenPrincipalId) {
            $this->command->error('No se encontró la tabla de almacenes (ni almacenes_fisicos ni almacenes). Ejecute migraciones.');
            return;
        }

        $tablaAlmacenes = $almacenPrincipalId->TABLE_NAME;
        
        $almacenId = DB::table($tablaAlmacenes)
            ->where('nombre', 'Almacén Principal')
            ->value('id_almacen');
        
        if (!$almacenId) {
            $this->command->error("No se encontró el 'Almacén Principal' en la tabla {$tablaAlmacenes}.");
            return;
        }
        
        foreach ($ingredientes as $data) {
            $ingredienteData = [
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'],
                'unidad_medida' => $data['unidad_medida'],
                'categoria' => $data['categoria'],
                'es_perecedero' => $data['es_perecedero'],
                'dias_vencimiento' => $data['dias_vencimiento'] ?? null,
                'estado' => $data['estado']
            ];
            
            $inventarioData = [
                'id_almacen' => $almacenId,
                'stock_actual' => $data['stock_inicial'],
                'stock_minimo' => $data['stock_minimo'],
                'stock_maximo' => $data['stock_maximo'],
                'unidad_medida' => $data['unidad_medida'],
                'costo_unitario_promedio' => $data['costo_unitario'],
                'fecha_ultimo_ingreso' => now(),
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            // Crear ingrediente
            $ingrediente = Ingrediente::create($ingredienteData);
            
            // Crear registro de inventario en almacén principal
            $inventarioData['id_ingrediente'] = $ingrediente->id_ingrediente;
            DB::table('inventario_almacen')->insert($inventarioData);
        }
        
        $this->command->info('Se han creado ' . count($ingredientes) . ' ingredientes con sus registros de inventario.');
    }
}
