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
     * Relación con inventario (uno a muchos)
     */
    public function inventarios()
    {
        return $this->hasMany(InventarioAlmacen::class, 'id_almacen', 'id_almacen');
    }

    /**
     * Relación con compras (uno a muchos)
     */
    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_almacen_destino', 'id_almacen');
    }

    /**
     * Obtener inventario de un ingrediente específico
     */
    public function inventarioIngrediente($idIngrediente)
    {
        return $this->inventarios()->where('id_ingrediente', $idIngrediente)->first();
    }

    /**
     * Obtener inventario con stock bajo
     */
    public function inventarioStockBajo()
    {
        return $this->inventarios()->stockBajo()->with('ingrediente');
    }

    /**
     * Obtener inventario agotado
     */
    public function inventarioAgotado()
    {
        return $this->inventarios()->agotados()->with('ingrediente');
    }

    /**
     * Scope para almacenes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Obtener valor total del inventario del almacén
     */
    public function getValorTotalInventarioAttribute()
    {
        return $this->inventarios->sum('valor_total_stock');
    }
}