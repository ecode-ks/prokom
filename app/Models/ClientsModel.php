<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientsModel extends Model
{
    protected $table = 'clients';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'client',
        'phone',
        'location'
    ];
}
