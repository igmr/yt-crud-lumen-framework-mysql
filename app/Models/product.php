<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  //* Nombre de tabla
  protected $table = 'Products';
  //* Clave primaria
  protected $primaryKey = 'id';
  //* Campos visibles
  protected $fillable = [
    'code',
    'description',
    'category',
    'brand',
    'type_product',
    'unit',
    'color',
    'weight',
    'price',
    'size',
  ];
  //* Campos ocultos
  protected $hidden = [
    'created_at',
    'updated_at',
    'deleted_at',
  ];
}
