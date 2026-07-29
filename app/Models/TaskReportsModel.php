<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskReportsModel extends Model
{
    protected $table = 'task_reports';
    protected $primaryKey = 'id';

    protected $fillable = [
        'date',
        'report',
        'images',
        'status',
        'reported_by',
        'task_id',
        'company_id',
    ];

    public $timestamps = false;
}
