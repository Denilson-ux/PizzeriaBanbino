<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacenes extends Model
{
    use HasFactory;

    protected $table = 'almacenes';
    protected $primaryKey = 'id_almacen';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'ubicacion',
        'responsable',
        'telefono',
        'estado'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con inventario del almacén
     */
    public function inventario()
    {
        return $this->hasMany(InventarioAlmacen::class, 'id_almacen', 'id_almacen');
    }

    /**
     * Relación con compras destinadas a este almacén
     */
    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_almacen_destino', 'id_almacen');
    }

    /**
     * Scope para almacenes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope para almacenes inactivos
     */
    public function scopeInactivos($query)
    {
        return $query->where('estado', 'inactivo');
    }

    /**
     * Obtener total de ingredientes en el almacén
     */
    public function getTotalIngredientesAttribute()
    {
        return $this->inventario()->where('estado', 'activo')->count();
    }

    /**
     * Obtener total de productos con stock
     */
    public function getProductosConStockAttribute()
    {
        return $this->inventario()->where('stock_actual', '>', 0)->count();
    }

    /**
     * Obtener productos con stock bajo
     */
    public function getProductosStockBajoAttribute()
    {
        return $this->inventario()->whereRaw('stock_actual <= stock_minimo')->count();
    }

    /**
     * Obtener valor total del inventario
     */
    public function getValorTotalInventarioAttribute()
    {
        return $this->inventario()
                    ->selectRaw('SUM(stock_actual * costo_unitario_promedio) as total')
                    ->first()->total ?? 0;
    }

    /**
     * Verificar si el almacén tiene ingredientes
     */
    public function tieneIngredientes()
    {
        return $this->inventario()->exists();
    }

    /**
     * Obtener ingredientes con stock bajo en este almacén
     */
    public function ingredientesStockBajo()
    {
        return $this->inventario()
                    ->with('ingrediente')
                    ->whereRaw('stock_actual <= stock_minimo')
                    ->where('estado', 'activo')
                    ->get();
    }

    /**
     * Obtener ingredientes agotados en este almacén
     */
    public function ingredientesAgotados()
    {
        return $this->inventario()
                    ->with('ingrediente')
                    ->where('stock_actual', '<=', 0)
                    ->where('estado', 'activo')
                    ->get();
    }
}