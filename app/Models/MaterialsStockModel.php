<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialsStockModel extends Model
{
    protected $table = 'materials_stock';
    protected $primaryKey = 'id';

    protected $fillable = [
        'material_id',
        'qty',
        'type'
    ];
}
