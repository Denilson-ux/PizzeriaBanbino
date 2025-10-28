<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;
    protected $table = 'rol';
    protected $primaryKey = 'id_rol';
    protected $fillable = [
        'nombre',
        'estado'
    ];
    public $timestamps = true;

    public function user() 
    {
        $this->hasMany(User::class, 'id_rol');
    }
}
