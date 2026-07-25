<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyNewsModel extends Model
{
    protected $table = 'company_news';
    protected $primaryKey = 'id';

    protected $fillable = [
        'info',
        'project_id'
    ];
}
