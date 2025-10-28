<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMenu extends Model
{
    use HasFactory;
    protected $table = 'item_menu';
    protected $primaryKey = 'id_item_menu';
    protected $fillable = [
        'nombre',
        'precio',
        'descripcion',
        'id_tipo_menu',
        'estado'
    ];
    public $timestamps = true;

    //Para la relación con tipoMenu
    public function tipoMenu()
    {
        return $this->belongsTo(TipoMenu::class, 'id_tipo_menu');
    }

    //para la relación de muchos a muchos con menú
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_item_menu', 'id_item_menu', 'id_menu');
    }

    public function detallePedido() 
    {
        return $this->hasMany(DetallePedido::class, 'id_item_menu');
    }
    
    public function detalleVenta() 
    {
        return $this->hasMany(DetalleVenta::class, 'id_item_menu');
    }
    
    // Relación con almacén (uno a uno)
    public function almacen()
    {
        return $this->hasOne(Almacen::class, 'id_item_menu');
    }
    
    // NUEVA: Relación con ingredientes (many-to-many) - RECETA
    public function recetas()
    {
        return $this->belongsToMany(Ingrediente::class, 'recetas', 'id_item_menu', 'id_ingrediente')
                    ->withPivot('cantidad_necesaria', 'unidad_receta', 'observaciones')
                    ->withTimestamps();
    }
    
    // Método para obtener el stock actual
    public function getStockActualAttribute()
    {
        return $this->almacen ? $this->almacen->stock_actual : 0;
    }
    
    // Método para verificar disponibilidad considerando ingredientes
    public function estaDisponible($cantidad = 1)
    {
        // Si tiene almacén propio (producto terminado)
        if ($this->almacen) {
            return $this->almacen->hayStockSuficiente($cantidad);
        }
        
        // Si no tiene almacén, verificar ingredientes
        if ($this->recetas->count() > 0) {
            return $this->ingredientesDisponibles($cantidad);
        }
        
        return false;
    }
    
    // NUEVO: Verificar si todos los ingredientes están disponibles
    public function ingredientesDisponibles($cantidad = 1)
    {
        foreach ($this->recetas as $receta) {
            $ingrediente = $receta->ingrediente;
            $cantidadNecesaria = $receta->pivot->cantidad_necesaria * $cantidad;
            
            if (!$ingrediente->tieneStock($cantidadNecesaria)) {
                return false;
            }
        }
        
        return true;
    }
    
    // NUEVO: Obtener la cantidad máxima que se puede preparar
    public function getCantidadMaximaDisponibleAttribute()
    {
        // Si tiene almacén propio
        if ($this->almacen) {
            return $this->almacen->stock_actual;
        }
        
        // Si usa ingredientes, calcular basado en el ingrediente más limitante
        if ($this->recetas->count() === 0) {
            return 0;
        }
        
        $cantidadMaxima = PHP_INT_MAX;
        
        foreach ($this->recetas as $receta) {
            $ingrediente = $receta->ingrediente;
            $stockDisponible = $ingrediente->almacen ? $ingrediente->almacen->stock_actual : 0;
            $cantidadPorPlato = $receta->pivot->cantidad_necesaria;
            
            if ($cantidadPorPlato > 0) {
                $platosDisponibles = floor($stockDisponible / $cantidadPorPlato);
                $cantidadMaxima = min($cantidadMaxima, $platosDisponibles);
            }
        }
        
        return $cantidadMaxima === PHP_INT_MAX ? 0 : $cantidadMaxima;
    }
    
    // NUEVO: Obtener costo total de ingredientes por plato
    public function getCostoIngredientesAttribute()
    {
        $costoTotal = 0;
        
        foreach ($this->recetas as $receta) {
            $ingrediente = $receta->ingrediente;
            $cantidadNecesaria = $receta->pivot->cantidad_necesaria;
            $costoIngrediente = $ingrediente->almacen ? $ingrediente->almacen->costo_unitario : 0;
            
            $costoTotal += $cantidadNecesaria * $costoIngrediente;
        }
        
        return $costoTotal;
    }
    
    // Scope para productos con stock
    public function scopeConStock($query)
    {
        return $query->whereHas('almacen', function($q) {
            $q->where('stock_actual', '>', 0);
        });
    }
    
    // Scope para productos sin stock
    public function scopeSinStock($query)
    {
        return $query->whereHas('almacen', function($q) {
            $q->where('stock_actual', '<=', 0);
        });
    }
    
    // NUEVO: Scope para items que son pizzas (tienen ingredientes)
    public function scopePizzasConReceta($query)
    {
        return $query->whereHas('recetas');
    }
    
    // NUEVO: Scope para items preparables (tienen ingredientes disponibles)
    public function scopePreparables($query, $cantidad = 1)
    {
        return $query->whereHas('recetas', function($q) use ($cantidad) {
            $q->whereHas('ingrediente.almacen', function($almacen) use ($cantidad) {
                $almacen->whereRaw('stock_actual >= (cantidad_necesaria * ?)', [$cantidad]);
            });
        });
    }
}