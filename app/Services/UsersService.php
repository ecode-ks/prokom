<?php

namespace App\Services;

use App\Repositories\UsersRepository;

class UsersService
{
    //---------------
    protected UsersRepository $usersRepository;
    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }
    //---------------
    public function checkAdminExists(): bool
    {
        return $this->usersRepository->checkAdminExists();
    }
}
