<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffersModel extends Model
{
    protected $table = 'offers';
    protected $primaryKey = 'id';

    protected $fillable = [
        'offer_number',
        'date',
        'client',
        'phone',
        'location',
        'service',
        'price',
        'status',
    ];
}
