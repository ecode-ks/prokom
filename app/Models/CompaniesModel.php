<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class CompaniesModel extends Authenticatable
{
    use HasApiTokens;

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

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
