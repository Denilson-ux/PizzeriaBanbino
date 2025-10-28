<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoAlmacen extends Model
{
    use HasFactory;
    
    protected $table = 'movimientos_almacen';
    protected $primaryKey = 'id_movimiento';
    
    protected $fillable = [
        'id_almacen',
        'tipo_movimiento', // entrada, salida, ajuste
        'cantidad',
        'stock_anterior',
        'stock_nuevo',
        'motivo', // venta, compra, devolucion, ajuste, merma
        'fecha_movimiento',
        'usuario_id',
        'referencia_id', // ID del pedido, venta, etc.
        'referencia_tipo', // pedido, venta, ajuste
        'observaciones'
    ];
    
    protected $casts = [
        'fecha_movimiento' => 'datetime'
    ];
    
    public $timestamps = true;
    
    // Relación con almacén
    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen');
    }
    
    // Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
    // Scope para movimientos por tipo
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_movimiento', $tipo);
    }
    
    // Scope para movimientos por fecha
    public function scopePorFecha($query, $fechaInicio, $fechaFin = null)
    {
        if ($fechaFin) {
            return $query->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin]);
        }
        return $query->whereDate('fecha_movimiento', $fechaInicio);
    }
    
    // Scope para entradas
    public function scopeEntradas($query)
    {
        return $query->where('tipo_movimiento', 'entrada');
    }
    
    // Scope para salidas
    public function scopeSalidas($query)
    {
        return $query->where('tipo_movimiento', 'salida');
    }
}