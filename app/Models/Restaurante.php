<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurante extends Model
{
    use HasFactory;
    protected $table = 'restaurante';
    protected $primaryKey = 'id_restaurante';
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'correo',
        'horario_apertura',
        'horario_cierre',
        'descripcion',
        'imagen',
        'latitud',
        'longitud',
    ];
    public $timestamps = true;
}
