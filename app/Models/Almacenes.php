<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacenes extends Model
{
    use HasFactory;

    protected $table = 'almacenes_fisicos';
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

    // Relaciones
    public function inventarios()
    {
        return $this->hasMany(InventarioAlmacen::class, 'id_almacen', 'id_almacen');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_almacen_destino', 'id_almacen');
    }

    // Scopes de estado
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeInactivos($query)
    {
        return $query->where('estado', 'inactivo');
    }

    // Atributos calculados
    public function getValorTotalInventarioAttribute()
    {
        return $this->inventarios->sum('valor_total_stock');
    }

    public function getTotalIngredientesAttribute()
    {
        return $this->inventarios()->count();
    }

    public function getProductosConStockAttribute()
    {
        return $this->inventarios()->where('stock_actual', '>', 0)->count();
    }

    public function getProductosStockBajoAttribute()
    {
        return $this->inventarios()->whereRaw('stock_actual <= stock_minimo')->count();
    }

    // Utilidades
    public function tieneIngredientes(): bool
    {
        return $this->inventarios()->exists();
    }
}
