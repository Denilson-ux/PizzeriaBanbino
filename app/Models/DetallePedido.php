<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\AlmacenService;

class DetallePedido extends Model
{
    use HasFactory;
    protected $table = 'detalle_pedido';
    //protected $primaryKey = ['id_nota_venta', 'id_item_menu', 'id_menu'];
    protected $fillable = [
        'id_pedido', 
        'id_menu', 
        'id_item_menu',
        'sub_monto', 
        'cantidad', 
        'estado'
    ];
    public $timestamps = false;

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
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
        $almacenService = new AlmacenService();
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
            // Al crear un detalle, verificar y reducir stock si el pedido está confirmado
            if ($detalle->pedido && $detalle->pedido->estado === 'confirmado') {
                $almacenService = new AlmacenService();
                $almacenService->procesarPedido($detalle->id_pedido);
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
                        $almacen->reducirStock($diferencia, 'ajuste_pedido');
                    } elseif ($diferencia < 0) {
                        // Se redujo la cantidad, devolver stock
                        $almacen->aumentarStock(abs($diferencia), 'ajuste_pedido');
                    }
                }
            }
        });
    }
}