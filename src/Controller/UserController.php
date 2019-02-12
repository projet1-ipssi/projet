<?php

namespace App\Controller;

use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        $namepage = 'Dashboard';

        return $this->render('user/dashboard.html.twig', [
            'namepage' => $namepage,
        ]);
    }

    /**
     * @Route("/user/profile", name="user-profile")
     */
    public function profile(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $namepage = 'My account';

        $user = $this->getUser();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());

            if (null !== $password) {
                $user->setPassword($password);
            }

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Your profile update successfully !');

            return $this->redirectToRoute('user');
        }

        return $this->render('user/profile/profile.html.twig', [
            'form' => $form->createView(),
            'namepage' => $namepage,
        ]);
    }
}
