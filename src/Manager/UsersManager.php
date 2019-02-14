<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;

class UsersManager
{
    private $usersRepository;

    public function __construct(UserRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function findAll()
    {
        return $this->usersRepository->findAll();
    }

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return $this->usersRepository->find($id, $lockMode, $lockVersion);
    }

    //Get 3 last added User
    public function findLastUsers()
    {
        return $this->usersRepository->findLastUsers();
    }
}