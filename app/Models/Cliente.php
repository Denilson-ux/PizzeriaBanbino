<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    protected $table = 'cliente';
    protected $primaryKey = 'id_cliente';
    protected $fillable = [
        'id_cliente',
        'descuento',
        'compras_realizadas',
        'estado'
    ];
    public $timestamps = true;

    public function notaVenta()
    {
        return $this->hasMany(NotaVenta::class, 'id_cliente');
    }

    public function pedido()
    {
        return $this->hasMany(Pedido::class, 'id_cliente');
    }
}
