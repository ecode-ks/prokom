<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class UsersModel extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user',
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
