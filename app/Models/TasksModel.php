<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TasksModel extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'id';

    protected $fillable = [
        'assigned_to',
        'assigned_by',
        'project_id',
        'deadline',
        'title',
        'task',
        'status',
        'company_id',
    ];
}
