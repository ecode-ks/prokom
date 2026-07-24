<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialsModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';

    protected $fillable = [
        'material',
        'unit',
    ];
}
