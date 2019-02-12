<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\AddEventType;
use App\Form\RegisterUserType;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    private $userRepository;
    private $eventRepository;
    private $em;

    public function __construct(UserRepository $userRepository, EventRepository $eventRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->eventRepository = $eventRepository;
        $this->em = $em;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $namepage = 'Dashboard';
        return $this->render('admin/dashboard.html.twig', [
            'namepage' => $namepage,
        ]);
    }

    /**
     * @Route("/admin/profile", name="admin-profile")
     */
    public function profile(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $namepage = 'My account';
        $user = $this->getUser();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Your profile update successfully !');
            return $this->redirectToRoute('admin-profile');
        }
        return $this->render('admin/user/profile.html.twig', [
            'form' => $form->createView(),
            'namepage' => $namepage,
        ]);
    }

    /**
     * @Route("/admin/users", name="all-users")
     */
    public function users(UserRepository $userRepository)
    {
        $namepage = 'All Users';
        $users = $userRepository->findAll();
        return $this->render('admin/user/all.html.twig', [
            'users' => $users,
            'namepage' => $namepage,
        ]);
    }

    /**
     * @Route("/admin/users/update/{id}", name="user-update")
     */
    public function updateUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $namepage = 'Update User';
        $user = $this->getUser();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'You profile successfully update!');
        }
        return $this->render('admin/user/update.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'namepage' => $namepage,
        ]);
    }

    /**
     * @Route("/admin/user/add", name="add-user")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $namepage = 'Add User';
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Registered !');
            $this->redirectToRoute('home');
        }

        return $this->render('admin/user/add.html.twig', [
            'form' => $form->createView(),
            'namepage' => $namepage,
        ]);
    }

    /**
     * @Route("/admin/users/remove/{id}", name="user-remove")
     */
    public function removeUser($id)
    {
        $user = $this->userRepository->find($id);
        $this->em->remove($user);
        $this->em->flush();
        $this->addFlash('success', 'You are successfully remove user!');
        return $this->redirectToRoute('all-users');
    }

    /**
     * @Route("/admin/events", name="all-event")
     */
    public function video()
    {
        $namepage = 'All Event';
        $events = $this->eventRepository->findAll();

        return $this->render('admin/event/all.html.twig', [
            'events' => $events,
            'namepage' => $namepage,
        ]);
    }


    /**
     * @Route("admin/event/add", name="add-event")
     */
    public function addEvent(Request $request)
    {
        $event = new Event();
        $form = $this->createForm(AddEventType::class, $event);
        $form->handleRequest($request);
        $namepage = 'Add Event';

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($event);
            $this->em->flush();
            $this->addFlash('success', 'Event added successfully !');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/event/add.html.twig', [
            'form' => $form->createView(),
            'namepage' => $namepage,
        ]);
    }

    /**
     * @Route("/admin/update/event/{id}", name="update-event")
     */
    public function adminUpdateVideo(Request $request, $id)
    {
        $namepage = 'Update Event';
        $event = $this->eventRepository->find($id);

        $form = $this->createForm(AddEventType::class, $event);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($event);
            $this->em->flush();
            $this->addFlash('success', 'You are successfully update Event !');
            return $this->redirectToRoute('admin');
        }
        return $this->render('admin/event/update.html.twig', [
            'form' => $form->createView(),
            'namepage' => $namepage,
            'event' => $event,
        ]);
    }

    /**
     * @Route("admin/event/remove/{id}", name="event-remove")
     */
    public function removeVideo($id)
    {
        $event = $this->eventRepository->find($id);

        $this->em->remove($event);
        $this->em->flush();

        $this->addFlash('success', 'You are successfully remove Event!');
        return $this->redirectToRoute('admin');
    }
}
