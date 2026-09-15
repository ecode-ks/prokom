<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class EmployeesModel extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'employees';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee',
        'phone',
        'email',
        'username',
        'password',
        'role',
        'company_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
