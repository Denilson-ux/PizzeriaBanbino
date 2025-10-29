<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingrediente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ingredientes';
    protected $primaryKey = 'id_ingrediente';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'unidad_medida',
        'estado',
        'categoria',
        'es_perecedero',
        'dias_vencimiento'
    ];

    protected $casts = [
        'es_perecedero' => 'boolean',
        'dias_vencimiento' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Relación con inventarios (uno a muchos)
     */
    public function inventarios()
    {
        return $this->hasMany(InventarioAlmacen::class, 'id_ingrediente', 'id_ingrediente');
    }

    /**
     * Relación con inventario principal (almacén principal)
     */
    public function inventarioPrincipal()
    {
        return $this->hasOne(InventarioAlmacen::class, 'id_ingrediente', 'id_ingrediente')
                   ->whereHas('almacen', function($query) {
                       $query->where('nombre', 'Almacén Principal');
                   });
    }

    /**
     * Relación con movimientos de almacén
     */
    public function movimientos()
    {
        return $this->hasMany(MovimientoAlmacen::class, 'id_ingrediente', 'id_ingrediente');
    }

    /**
     * Relación con recetas (muchos a muchos con ItemMenu)
     */
    public function itemsMenu()
    {
        return $this->belongsToMany(
            ItemMenu::class, 
            'item_menu_ingredientes', 
            'id_ingrediente', 
            'id_item_menu'
        )->withPivot('cantidad_necesaria', 'unidad_medida', 'notas', 'es_opcional')
          ->withTimestamps();
    }

    /**
     * Relación con detalles de compra
     */
    public function detallesCompra()
    {
        return $this->hasMany(DetalleCompra::class, 'id_ingrediente', 'id_ingrediente');
    }

    /**
     * Obtener stock actual del ingrediente (del almacén principal)
     */
    public function getStockActualAttribute()
    {
        return $this->inventarioPrincipal ? $this->inventarioPrincipal->stock_actual : 0;
    }

    /**
     * Verificar si hay stock suficiente
     */
    public function tieneStockSuficiente($cantidadRequerida)
    {
        return $this->stock_actual >= $cantidadRequerida;
    }

    /**
     * Verificar si el stock está bajo
     */
    public function getStockBajoAttribute()
    {
        if (!$this->inventarioPrincipal) return false;
        
        return $this->inventarioPrincipal->stock_actual <= $this->inventarioPrincipal->stock_minimo;
    }

    /**
     * Scope para ingredientes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope para ingredientes con stock bajo
     */
    public function scopeStockBajo($query)
    {
        return $query->whereHas('inventarios', function($q) {
            $q->whereRaw('stock_actual <= stock_minimo');
        });
    }

    /**
     * Scope para ingredientes por categoría
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope para ingredientes perecederos
     */
    public function scopePerecederos($query)
    {
        return $query->where('es_perecedero', true);
    }
}