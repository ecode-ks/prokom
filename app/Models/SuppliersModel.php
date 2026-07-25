<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuppliersModel extends Model
{
    protected $table = 'suppliers';
    protected $primaryKey = 'id';

    protected $fillable = [
        'supplier',
        'phone',
        'location'
    ];
}
