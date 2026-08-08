<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalariesModel extends Model
{
    protected $table = 'salaries';
    protected $primaryKey = 'id';

    protected $fillable = [
        'date',
        'employee_id',
        'salary'
    ];
}
