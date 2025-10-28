<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\AlmacenService;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = 'detalle_venta';
    //protected $primaryKey = ['id_nota_venta', 'id_item_menu', 'id_menu'];
    protected $fillable = [
        'id_nota_venta', 
        'id_item_menu', 
        'id_menu', 
        'sub_monto', 
        'cantidad', 
        'estado'
    ];
    public $timestamps = false;

    public function notaVenta()
    {
        return $this->belongsTo(NotaVenta::class, 'id_nota_venta');
    }

    public function menuItemMenu()
    {
        return $this->belongsTo(MenuItemMenu::class, ['id_item_menu', 'id_menu']);
    }
    
    public function itemMenu() 
    {
        return $this->belongsTo(ItemMenu::class, 'id_item_menu');
    }
    
    // Método para verificar disponibilidad antes de guardar
    public function verificarDisponibilidad()
    {
        $almacen = $this->itemMenu->almacen;
        
        if (!$almacen) {
            return ['disponible' => false, 'mensaje' => 'Producto no registrado en almacén'];
        }
        
        if (!$almacen->hayStockSuficiente($this->cantidad)) {
            return [
                'disponible' => false, 
                'mensaje' => "Stock insuficiente. Disponible: {$almacen->stock_actual}, solicitado: {$this->cantidad}"
            ];
        }
        
        return ['disponible' => true];
    }
    
    // Event listeners para actualizar stock automáticamente
    protected static function booted()
    {
        static::created(function ($detalle) {
            // Al crear un detalle de venta, reducir stock inmediatamente
            $almacenService = new AlmacenService();
            $resultado = $almacenService->procesarVenta($detalle->id_nota_venta);
            
            if (!$resultado['success']) {
                // Log error or handle as needed
                \Log::warning('Error al reducir stock en venta: ', $resultado['errores']);
            }
        });
        
        static::updated(function ($detalle) {
            // Al actualizar cantidad, ajustar el stock correspondientemente
            if ($detalle->isDirty('cantidad')) {
                $almacen = $detalle->itemMenu->almacen;
                if ($almacen) {
                    $cantidadOriginal = $detalle->getOriginal('cantidad');
                    $nuevaCantidad = $detalle->cantidad;
                    $diferencia = $nuevaCantidad - $cantidadOriginal;
                    
                    if ($diferencia > 0) {
                        // Se aumentó la cantidad, reducir más stock
                        $almacen->reducirStock($diferencia, 'ajuste_venta');
                    } elseif ($diferencia < 0) {
                        // Se redujo la cantidad, devolver stock
                        $almacen->aumentarStock(abs($diferencia), 'ajuste_venta');
                    }
                }
            }
        });
        
        static::deleted(function ($detalle) {
            // Al eliminar un detalle, devolver el stock
            $almacen = $detalle->itemMenu->almacen;
            if ($almacen) {
                $almacen->aumentarStock($detalle->cantidad, 'devolucion');
            }
        });
    }
}