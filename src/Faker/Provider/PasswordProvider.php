<?php

namespace App\Faker\Provider;

use App\Entity\User;
use Faker\Generator;
use Faker\Provider\Base as BaseProvider;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordProvider extends BaseProvider
{
    private $passwordEncoder;

    public function __construct(Generator $generator, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($generator);

        $this->passwordEncoder = $passwordEncoder;
    }

    public function encodePassword(User $user, $password)
    {
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $password);
        return $encodedPassword;
    }

}