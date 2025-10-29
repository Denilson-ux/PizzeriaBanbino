<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class FixCurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🔄 Actualizando símbolos de moneda en la base de datos...\n";
        
        // Actualizar configuraciones de moneda si existen en settings
        try {
            $tables = ['settings', 'configuraciones', 'opciones'];
            
            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $updated = DB::table($table)
                        ->where('valor', 'like', '%S/.%')
                        ->orWhere('valor', 'like', '%S/%')
                        ->update([
                            'valor' => DB::raw("REPLACE(REPLACE(valor, 'S/.', 'Bs'), 'S/', 'Bs')")
                        ]);
                    
                    if ($updated > 0) {
                        echo "✅ Actualizados {$updated} registros en tabla {$table}\n";
                    }
                }
            }
        } catch (\Exception $e) {
            echo "⚠️  No se encontraron tablas de configuración específicas\n";
        }
        
        // Actualizar campos de texto que puedan contener símbolos de moneda
        try {
            $fieldsToUpdate = [
                'productos' => ['descripcion', 'precio_texto'],
                'items_menu' => ['descripcion'],
                'menus' => ['descripcion'],
                'compras' => ['observaciones'],
                'pedidos' => ['observaciones', 'descripcion'],
                'proveedores' => ['observaciones'],
                'clientes' => ['observaciones']
            ];
            
            foreach ($fieldsToUpdate as $table => $fields) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    foreach ($fields as $field) {
                        if (DB::getSchemaBuilder()->hasColumn($table, $field)) {
                            $updated = DB::table($table)
                                ->whereNotNull($field)
                                ->where(function($query) use ($field) {
                                    $query->where($field, 'like', '%S/.%')
                                          ->orWhere($field, 'like', '%S/%');
                                })
                                ->update([
                                    $field => DB::raw("REPLACE(REPLACE({$field}, 'S/.', 'Bs'), 'S/', 'Bs')")
                                ]);
                            
                            if ($updated > 0) {
                                echo "✅ Actualizados {$updated} registros en {$table}.{$field}\n";
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            echo "⚠️  Error actualizando campos de texto: " . $e->getMessage() . "\n";
        }
        
        echo "\n💰 Configurando moneda boliviana como predeterminada...\n";
        
        // Verificar si existe tabla de configuración del sistema
        if (DB::getSchemaBuilder()->hasTable('configuraciones')) {
            DB::table('configuraciones')->updateOrInsert(
                ['clave' => 'moneda_simbolo'],
                ['valor' => 'Bs', 'descripcion' => 'Símbolo de moneda del sistema']
            );
            
            DB::table('configuraciones')->updateOrInsert(
                ['clave' => 'moneda_nombre'],
                ['valor' => 'Bolivianos', 'descripcion' => 'Nombre de la moneda del sistema']
            );
            
            DB::table('configuraciones')->updateOrInsert(
                ['clave' => 'moneda_codigo'],
                ['valor' => 'BOB', 'descripcion' => 'Código ISO de la moneda']
            );
            
            echo "✅ Configuración de moneda boliviana guardada\n";
        }
        
        echo "\n🎉 ¡Actualización de moneda completada!\n";
        echo "📋 Resumen de cambios:\n";
        echo "   • S/. → Bs\n";
        echo "   • S/ → Bs\n";
        echo "   • Moneda predeterminada: Bolivianos (BOB)\n";
        echo "\n💡 Tip: Limpia el caché de vistas con: php artisan view:clear\n\n";
    }
}