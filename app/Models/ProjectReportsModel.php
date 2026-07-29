<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectReportsModel extends Model
{
    protected $table = 'project_reports';
    protected $primaryKey = 'id';

    protected $fillable = [
        'report',
        'images',
        'status',
        'reported_by',
        'project_id'
    ];
}
