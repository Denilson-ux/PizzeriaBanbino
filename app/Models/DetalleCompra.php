<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    use HasFactory;
    
    protected $table = 'detalle_compras';
    protected $primaryKey = 'id_detalle_compra';
    
    protected $fillable = [
        'id_compra',
        'id_ingrediente',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'observaciones'
    ];
    
    protected $casts = [
        'cantidad' => 'decimal:3',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];
    
    public $timestamps = false;
    
    // Relación con compra
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_compra');
    }
    
    // Relación con ingrediente
    public function ingrediente()
    {
        return $this->belongsTo(Ingrediente::class, 'id_ingrediente');
    }
    
    // Método para obtener el nombre del ingrediente
    public function getNombreIngrediente()
    {
        return $this->ingrediente ? $this->ingrediente->nombre : 'Ingrediente no encontrado';
    }
    
    // Método para obtener la categoría del ingrediente
    public function getCategoriaIngrediente()
    {
        return $this->ingrediente ? ($this->ingrediente->categoria ?? 'Otros') : 'Sin categoría';
    }
    
    // Método para obtener la unidad de medida
    public function getUnidadMedida()
    {
        return $this->ingrediente ? $this->ingrediente->unidad_medida : 'unidades';
    }
    
    // Event listener para calcular subtotal automáticamente
    protected static function booted()
    {
        static::creating(function ($detalle) {
            $detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
        });
        
        static::updating(function ($detalle) {
            if ($detalle->isDirty(['cantidad', 'precio_unitario'])) {
                $detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
            }
        });
        
        static::saved(function ($detalle) {
            // Recalcular totales de la compra
            if ($detalle->compra) {
                $detalle->compra->calcularTotales();
            }
        });
        
        static::deleted(function ($detalle) {
            // Recalcular totales de la compra al eliminar detalle
            if ($detalle->compra) {
                $detalle->compra->calcularTotales();
            }
        });
    }
    
    // Método para obtener el total de este detalle
    public function getTotal()
    {
        return $this->cantidad * $this->precio_unitario;
    }
    
    // Método para verificar si el ingrediente existe en almacén
    public function tieneAlmacen()
    {
        return $this->ingrediente && $this->ingrediente->almacen !== null;
    }
    
    // Método para obtener el stock actual del ingrediente
    public function getStockActual()
    {
        if ($this->ingrediente && $this->ingrediente->almacen) {
            return $this->ingrediente->almacen->stock_actual;
        }
        
        return 0;
    }
    
    // Método para obtener el registro de almacén asociado
    public function getAlmacen()
    {
        return $this->ingrediente ? $this->ingrediente->almacen : null;
    }
    
    // Método para verificar si el stock está bajo
    public function isStockBajo()
    {
        return $this->ingrediente ? $this->ingrediente->stock_bajo : false;
    }
}