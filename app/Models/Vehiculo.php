<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;
    protected $table = 'vehiculo';
    protected $primaryKey = 'id_vehiculo';
    protected $fillable = [
        'placa',
        'marca',
        'modelo',
        'color',
        'anio',
        'id_repartidor',
        'id_tipo_vehiculo',
        'estado'
    ];
    public $timestamps = true;

    //Para la relación con tipo vehiculo
    public function tipoVehiculo()
    {
        return $this->belongsTo(TipoVehiculo::class, 'id_tipo_vehiculo');
    }

    public function repartidor()
    {
        return $this->belongsTo(Repartidor::class, 'id_repartidor');
    }
}
