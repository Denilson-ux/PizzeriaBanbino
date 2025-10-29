<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioAlmacen extends Model
{
    use HasFactory;

    protected $table = 'inventario_almacen';
    protected $primaryKey = 'id_inventario';
    
    protected $fillable = [
        'id_almacen',
        'id_ingrediente',
        'stock_actual',
        'stock_minimo',
        'stock_maximo',
        'unidad_medida',
        'ubicacion_fisica',
        'costo_unitario_promedio',
        'fecha_ultimo_ingreso',
        'fecha_ultimo_egreso',
        'fecha_vencimiento',
        'estado'
    ];

    protected $casts = [
        'stock_actual' => 'decimal:3',
        'stock_minimo' => 'decimal:3',
        'stock_maximo' => 'decimal:3',
        'costo_unitario_promedio' => 'decimal:2',
        'fecha_ultimo_ingreso' => 'date',
        'fecha_ultimo_egreso' => 'date',
        'fecha_vencimiento' => 'date'
    ];

    /**
     * Relación con almacén físico
     */
    public function almacen()
    {
        return $this->belongsTo(Almacenes::class, 'id_almacen', 'id_almacen');
    }

    /**
     * Relación con ingrediente
     */
    public function ingrediente()
    {
        return $this->belongsTo(Ingrediente::class, 'id_ingrediente', 'id_ingrediente');
    }

    /**
     * Verificar si el stock está bajo
     */
    public function getStockBajoAttribute()
    {
        return $this->stock_actual <= $this->stock_minimo;
    }

    /**
     * Obtener estado del stock
     */
    public function getEstadoStockAttribute()
    {
        if ($this->stock_actual <= 0) {
            return 'agotado';
        } elseif ($this->stock_actual <= $this->stock_minimo) {
            return 'bajo';
        } elseif ($this->stock_actual >= $this->stock_maximo) {
            return 'exceso';
        } else {
            return 'normal';
        }
    }

    /**
     * Aumentar stock (por compras)
     */
    public function aumentarStock($cantidad, $costoUnitario = null)
    {
        $stockAnterior = $this->stock_actual;
        
        // Actualizar costo promedio ponderado si se proporciona costo
        if ($costoUnitario !== null && $cantidad > 0) {
            $valorStockAnterior = $stockAnterior * $this->costo_unitario_promedio;
            $valorNuevoStock = $cantidad * $costoUnitario;
            $stockTotal = $stockAnterior + $cantidad;
            
            if ($stockTotal > 0) {
                $this->costo_unitario_promedio = ($valorStockAnterior + $valorNuevoStock) / $stockTotal;
            }
        }
        
        $this->stock_actual += $cantidad;
        $this->fecha_ultimo_ingreso = now();
        $this->save();
        
        return $this;
    }

    /**
     * Reducir stock (por ventas)
     */
    public function reducirStock($cantidad)
    {
        if ($this->stock_actual < $cantidad) {
            throw new \Exception("Stock insuficiente para el ingrediente: {$this->ingrediente->nombre}. Stock actual: {$this->stock_actual}, Requerido: {$cantidad}");
        }
        
        $this->stock_actual -= $cantidad;
        $this->fecha_ultimo_egreso = now();
        $this->save();
        
        return $this;
    }

    /**
     * Verificar si hay stock suficiente
     */
    public function tieneStockSuficiente($cantidadRequerida)
    {
        return $this->stock_actual >= $cantidadRequerida;
    }

    /**
     * Scope para stock bajo
     */
    public function scopeStockBajo($query)
    {
        return $query->whereRaw('stock_actual <= stock_minimo');
    }

    /**
     * Scope para productos agotados
     */
    public function scopeAgotados($query)
    {
        return $query->where('stock_actual', '<=', 0);
    }

    /**
     * Scope para productos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Obtener valor total del stock
     */
    public function getValorTotalStockAttribute()
    {
        return $this->stock_actual * $this->costo_unitario_promedio;
    }
}