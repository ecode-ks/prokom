<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraHoursModel extends Model
{
    protected $table = 'extra_hours';
    protected $primaryKey = 'id';

    protected $fillable = [
        'date',
        'employee_id',
        'hours',
        'type',
        'company_id',
    ];
}
