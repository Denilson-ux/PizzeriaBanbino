<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'proveedores';
    protected $primaryKey = 'id_proveedor';
    
    protected $fillable = [
        'nombre',
        'ruc',
        'telefono',
        'email',
        'direccion',
        'contacto',
        'estado'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];
    
    public $timestamps = true;
    
    // Relación con compras
    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_proveedor');
    }
    
    // Scope para proveedores activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
    
    // Scope para proveedores inactivos
    public function scopeInactivos($query)
    {
        return $query->where('estado', 'inactivo');
    }
    
    // Método para obtener el nombre completo con RUC
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' (' . $this->ruc . ')';
    }
    
    // Método para obtener total de compras
    public function getTotalComprasAttribute()
    {
        return $this->compras()->where('estado', 'completada')->sum('total');
    }
    
    // Método para obtener última compra
    public function getUltimaCompraAttribute()
    {
        return $this->compras()->latest()->first();
    }
}