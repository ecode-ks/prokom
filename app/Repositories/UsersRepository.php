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
    //---------------
    public function checkUserExists(string $username): bool
    {
        return DB::table($this->table)
            ->where('username', $username)
            ->exists();
    }
    //---------------
    public function registerAdmin(array $data): bool
    {
        return DB::table($this->table)
            ->insert($data);
    }
    //---------------
    public function checkUser(array $credentials)
    {
        return UsersModel::where(
            'username',
            $credentials['username']
        )->first();
    }
    //---------------
    public function findUserByUsername(string $username)
    {
        return UsersModel::where(
            'username',
            $username
        )->first();
    }
    //---------------
    public function updatePasswordByUsername(string $username, string $password): bool
    {
        return UsersModel::where(
                'username',
                $username
            )->update([
                'password' => $password,
            ]) > 0;
    }
}
