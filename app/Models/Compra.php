<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compra extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'compras';
    protected $primaryKey = 'id_compra';
    
    protected $fillable = [
        'numero_compra',
        'id_proveedor',
        'id_almacen_destino', // Nuevo campo para almacén destino
        'id_usuario', // Usuario que registra la compra
        'fecha_compra',
        'fecha_entrega',
        'subtotal',
        'impuesto',
        'total',
        'estado', // pendiente, completada, cancelada
        'tipo_compra', // contado, credito
        'observaciones',
        'numero_factura',
        'aplicar_almacen' // true = automático, false = manual
    ];
    
    protected $casts = [
        'fecha_compra' => 'date',
        'fecha_entrega' => 'date',
        'subtotal' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
        'aplicar_almacen' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];
    
    public $timestamps = true;
    
    // Relación con proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }
    
    // Relación con almacén destino
    public function almacenDestino()
    {
        return $this->belongsTo(Almacenes::class, 'id_almacen_destino', 'id_almacen');
    }
    
    // Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    
    // Relación con detalles de compra
    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'id_compra');
    }
    
    // Scope para compras por estado
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }
    
    // Scope para compras pendientes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }
    
    // Scope para compras completadas
    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }
    
    // Scope para compras de hoy
    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_compra', today());
    }
    
    // Scope para compras del mes
    public function scopeDelMes($query)
    {
        return $query->whereMonth('fecha_compra', now()->month)
                    ->whereYear('fecha_compra', now()->year);
    }
    
    // Método para generar número de compra automático
    public static function generarNumeroCompra()
    {
        $ultimaCompra = self::latest()->first();
        $numero = $ultimaCompra ? (int) substr($ultimaCompra->numero_compra, 2) + 1 : 1;
        return 'CO' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
    
    // Método para calcular totales
    public function calcularTotales()
    {
        $subtotal = $this->detalles->sum(function($detalle) {
            return $detalle->cantidad * $detalle->precio_unitario;
        });
        
        $impuesto = $subtotal * 0.18; // IGV 18%
        $total = $subtotal + $impuesto;
        
        $this->update([
            'subtotal' => $subtotal,
            'impuesto' => $impuesto,
            'total' => $total
        ]);
        
        return $this;
    }
    
    // Método para completar compra y actualizar inventario del almacén
    public function completarCompra()
    {
        if ($this->estado !== 'pendiente') {
            return ['success' => false, 'mensaje' => 'Solo se pueden completar compras pendientes'];
        }
        
        if (!$this->id_almacen_destino) {
            return ['success' => false, 'mensaje' => 'Debe especificar un almacén destino para completar la compra'];
        }
        
        try {
            \DB::beginTransaction();
            
            // Actualizar estado
            $this->update(['estado' => 'completada']);
            
            // Si debe aplicarse automáticamente al inventario
            if ($this->aplicar_almacen) {
                foreach ($this->detalles as $detalle) {
                    // Buscar o crear inventario por almacén e ingrediente
                    $inventario = InventarioAlmacen::firstOrCreate(
                        [
                            'id_almacen' => $this->id_almacen_destino,
                            'id_ingrediente' => $detalle->id_ingrediente
                        ],
                        [
                            'stock_actual' => 0,
                            'stock_minimo' => 5,
                            'stock_maximo' => 100,
                            'unidad_medida' => $detalle->ingrediente->unidad_medida,
                            'costo_unitario_promedio' => 0,
                            'estado' => 'activo'
                        ]
                    );
                    
                    // Actualizar stock usando método del modelo InventarioAlmacen
                    $inventario->aumentarStock($detalle->cantidad, $detalle->precio_unitario);
                }
            }
            
            \DB::commit();
            return ['success' => true, 'mensaje' => 'Compra completada exitosamente. Inventario actualizado en ' . $this->almacenDestino->nombre];
            
        } catch (\Exception $e) {
            \DB::rollback();
            return ['success' => false, 'mensaje' => 'Error al completar compra: ' . $e->getMessage()];
        }
    }
    
    // Método para obtener el estado con badge HTML
    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'pendiente' => '<span class="badge badge-warning">Pendiente</span>',
            'completada' => '<span class="badge badge-success">Completada</span>',
            'cancelada' => '<span class="badge badge-danger">Cancelada</span>'
        ];
        
        return $badges[$this->estado] ?? '<span class="badge badge-secondary">Desconocido</span>';
    }
}