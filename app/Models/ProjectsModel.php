<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectsModel extends Model
{
    protected $table = 'projects';
    protected $primaryKey = 'id';

    protected $fillable = [
        'project_name',
        'project_manager',
        'client_id',
        'location',
        'start_date',
        'expected_end_date',
        'budget',
        'description',
        'status'
    ];
}
