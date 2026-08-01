<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationsModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'recipient_type',
        'recipient_id',
        'type',
        'title',
        'message',
        'link',
        'is_read',
        'created_at',
    ];
}
