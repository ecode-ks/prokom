<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VacationsModel extends Model
{
    protected $table = 'vacations';
    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'from_date',
        'to_date',
        'comment',
        'type',
    ];
}
