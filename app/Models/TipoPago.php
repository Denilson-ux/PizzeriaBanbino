<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPago extends Model
{
    use HasFactory;
    protected $table = 'tipo_pago';
    protected $primaryKey = 'id_tipo_pago';
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];
    public $timestamps = true;

    public function notaVenta()
    {
        return $this->hasMany(NotaVenta::class, 'id_tipo_pago');
    }
}
