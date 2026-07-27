<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkingHoursModel extends Model
{
    protected $table = 'working_hours';
    protected $primaryKey = 'id';

    protected $fillable = [
        'date',
        'employee_id',
        'check_in',
        'check_out',
        'hours'
    ];
}
