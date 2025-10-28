<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;
    protected $table = 'pedido';
    protected $primaryKey = 'id_pedido';
    protected $fillable = [
        'fecha',
        'monto',
        'descripcion',
        'nro_transaccion',
        'descripcion_pago',
        'estado_pedido',
        'id_ubicacion',
        'id_tipo_pago',
        'id_cliente',
        'id_repartidor',
        'estado'
    ];

    public function ubicacion() 
    {
        return $this->belongsTo(Ubicacion::class, 'id_ubicacion');
    }

    public function repartidor() 
    {
        return $this->belongsTo(Repartidor::class, 'id_repartidor');
    }

    public function cliente() 
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function tipoPago() 
    {
        return $this->belongsTo(TipoPago::class, 'id_tipo_pago');
    }

    public function detallePedido()
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido');
    }
}
