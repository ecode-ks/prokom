<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckInModel extends Model
{
    protected $table = 'check_ins';
    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'check_in_time',
        'check_in_location',
        'pause_start_time',
        'pause_start_location',
        'pause_end_time',
        'pause_end_location',
        'check_out_time',
        'check_out_location',
        'status',
        'company_id',
    ];
}
