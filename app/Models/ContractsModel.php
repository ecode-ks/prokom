<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractsModel extends Model
{
    protected $table = 'contracts';
    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'position',
        'salary',
        'pay_type',
        'extra_hours_price',
        'weekend_hours_price',
        'from_date',
        'to_date',
        'docs'
    ];
}
