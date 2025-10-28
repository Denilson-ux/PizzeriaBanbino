<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaVenta extends Model
{
    use HasFactory;
    protected $table = 'nota_venta';
    protected $primaryKey = 'id_nota_venta';
    protected $fillable = [
        'monto',
        'fecha',
        'estado',
        'id_empleado',
        'id_cliente',
        'id_tipo_pago',
    ];
    public $timestamps = true;

    public function empleado() 
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function cliente() 
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function tipoPago() 
    {
        return $this->belongsTo(TipoPago::class, 'id_tipo_pago');
    }

    public function detalleVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'id_nota_venta');
    }
}
