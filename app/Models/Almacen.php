<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    use HasFactory;

    protected $table = 'almacen';
    protected $primaryKey = 'id_almacen';
    
    protected $fillable = [
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
     * Relación con ingrediente (uno a uno inversa)
     */
    public function ingrediente()
    {
        return $this->belongsTo(Ingrediente::class, 'id_ingrediente', 'id_ingrediente');
    }

    /**
     * Relación con movimientos de almacén
     */
    public function movimientos()
    {
        return $this->hasMany(MovimientoAlmacen::class, 'id_ingrediente', 'id_ingrediente');
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
     * Actualiza el costo promedio ponderado
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
     * Ajustar stock (para corrección de inventario)
     */
    public function ajustarStock($nuevoStock, $observaciones = null)
    {
        $stockAnterior = $this->stock_actual;
        $this->stock_actual = $nuevoStock;
        $this->save();
        
        // Registrar movimiento de ajuste
        MovimientoAlmacen::create([
            'id_ingrediente' => $this->id_ingrediente,
            'tipo_movimiento' => 'ajuste',
            'cantidad' => abs($nuevoStock - $stockAnterior),
            'unidad_medida' => $this->unidad_medida,
            'stock_anterior' => $stockAnterior,
            'stock_posterior' => $nuevoStock,
            'referencia_tipo' => 'ajuste_manual',
            'observaciones' => $observaciones,
            'usuario_id' => auth()->id(),
            'fecha_movimiento' => now()
        ]);
        
        return $this;
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
     * Scope para productos próximos a vencer
     */
    public function scopeProximosVencer($query, $dias = 7)
    {
        return $query->where('fecha_vencimiento', '<=', now()->addDays($dias))
                    ->whereNotNull('fecha_vencimiento');
    }

    /**
     * Obtener valor total del stock
     */
    public function getValorTotalStockAttribute()
    {
        return $this->stock_actual * $this->costo_unitario_promedio;
    }
}