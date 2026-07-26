<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeesModel extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'id';

    protected $fillable = [
        'employee',
        'phone',
        'email',
        'username',
        'password',
        'role',
    ];
}
