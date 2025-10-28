<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoVehiculo extends Model
{
    use HasFactory;
    protected $table = 'tipo_vehiculo';
    protected $primaryKey = 'id_tipo_vehiculo';
    protected $fillable = [
        'nombre',
        'estado'
    ];
    public $timestamps = true;

    public function vehiculo()
    {
        return $this->hasMany(Vehiculo::class, 'id_tipo_vehiculo');
    }
}
