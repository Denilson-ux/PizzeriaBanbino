<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItemMenu extends Model
{
    use HasFactory;
    protected $table = 'menu_item_menu';
    //protected $primaryKey = ['id_item_menu', 'id_menu'];
    protected $fillable = [
        'id_item_menu',
        'id_menu',
        'cantidad'
    ];
    public $timestamps = false;
    
    public function detalleVenta()
    {
        return $this->hasMany(DetalleVenta::class, ['id_item_menu', 'id_menu']);
    }

    public function detallePedido()
    {
        return $this->hasMany(DetallePedido::class, ['id_item_menu', 'id_menu']);
    }
}
