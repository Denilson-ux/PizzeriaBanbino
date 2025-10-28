<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $table = 'menu';
    protected $primaryKey = 'id_menu';
    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha',
        'estado',
    ];
    public $timestamps = true;

    //para la relacion de muchos a muchos
    public function itemMenus()
    {
        return $this->belongsToMany(ItemMenu::class, 'menu_item_menu', 'id_menu', 'id_item_menu') 
                    ->withPivot('cantidad');
    }
}
