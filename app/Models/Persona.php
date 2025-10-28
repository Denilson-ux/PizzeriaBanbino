<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;
    protected $table = 'persona';
    protected $primaryKey = 'id_persona';
    protected $fillable = [
        'ci',
        'nombre',
        'paterno',
        'materno',
        'telefono',
        'direccion',
        'correo',
        'imagen',
        'estado',
        'id_persona',
    ];
    public $timestamps = true;

    public function user() {
        return $this->hasMany(User::class, 'id_persona');
    }
}
