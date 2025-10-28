<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;
    protected $table = 'empleado';
    protected $primaryKey = 'id_empleado';
    protected $fillable = [
        'id_empleado',
        'sueldo',
        'estado'
    ];
    public $timestamps = true;

    public function notaVenta()
    {
        return $this->hasMany(NotaVenta::class, 'id_empleado');
    }
}
