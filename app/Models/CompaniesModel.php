<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompaniesModel extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'id';

    protected $fillable = [
        'company',
        'email',
        'phone',
        'location',
        'username',
        'password',
        'status',
        'payment_expire',
        'package',
        'language',
    ];
}
