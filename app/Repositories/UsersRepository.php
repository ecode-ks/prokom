<?php

namespace App\Repositories;

use App\Models\UsersModel;
use Illuminate\Support\Facades\DB;

class UsersRepository
{
    protected string $table;
    public function __construct(UsersModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function checkAdminExists(): bool
    {
        return DB::table($this->table)
            ->where('role', 'admin')
            ->exists();
    }
}
