<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackagesModel extends Model
{
    protected $table = 'packages';
    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
        'price',
        'months',
        'description'
    ];
}
