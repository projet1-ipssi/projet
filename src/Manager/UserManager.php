<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;

class UserManager
{
    private $userRepository;
    private $articleRepository;

    public function __construct(UserRepository $userRepository, ArticleRepository $articleRepository)
    {
        $this->userRepository = $userRepository;
        $this->articleRepository = $articleRepository;
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    public function getNumberArticle($email)
    {
        return $this->articleRepository->countArticle($email);
    }

    public function getUserArticle($email)
    {
        return $this->articleRepository->getUserArticle($email);
    }

    public function getUsersByFirstname(string $firstName): ?array
    {
        return $this->userRepository->findBy(['firstname' => $firstName], ['email' => 'ASC']);
    }
}