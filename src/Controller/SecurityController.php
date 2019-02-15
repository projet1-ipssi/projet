<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\RegisterUserType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\LoginUserType;

class SecurityController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();

        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'You are successfully Registered !');
            $this->redirectToRoute('home');

        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $user = new User();

        $form = $this->createForm(LoginUserType::class, $user);
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('security/login.html.twig', [
            'error' => $error ? $error->getMessage() : null,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login/success", name="login_success")
     */
    public function loginSuccess()
    {
        $user = $this->getUser();
        $role = $user->getRoles();
        if (in_array("ROLE_ADMIN", $role)) {
            $this->addFlash('success', 'Login success !');
            return $this->redirectToRoute('admin');
        } else {
            $this->addFlash('success', 'Login success !');
            return $this->redirectToRoute('user');
        }
    }


    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        if ($this->getUser()) {
            $this->get('security.token_storage')->setToken(null);
            $this->get('session')->invalidate();
        }
        $this->addFlash('success', 'User disconnected!');
        $this->redirectToRoute('home');
    }
}
