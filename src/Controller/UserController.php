<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Event;
use App\Form\RegisterUserType;
use App\Repository\CommentsRepository;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $userRepository;
    private $eventRepository;
    private $commentsRepository;
    private $em;

    public function __construct(UserRepository $userRepository, EventRepository $eventRepository, CommentsRepository $commentsRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->eventRepository = $eventRepository;
        $this->commentsRepository = $commentsRepository;
        $this->em = $em;
    }

    protected function prepareResult(Event $event)
    {
        $vote = false;
        $user = $this->getUser();
        if ($user) {
            $comment = $this->getDoctrine()->getRepository(Comments::class)->findOneBy(['user' => $user, 'event' => $event]);
            if ($comment) {
                $vote = true;
            }
        }
        $avg = $this->getDoctrine()->getRepository(Comments::class)->getMoyenne($event);

        return [
            'html' => $this->renderView('home/event.html.twig', [
                'event' => $event,
                'vote' => $vote,
                'moyenne'=>$avg
            ])
        ];
    }

    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        $namepage = 'Dashboard';
        $user = $this->getUser();
        $events = $this->commentsRepository->userLastRate($user);

        return $this->render('user/dashboard.html.twig', [
            'namepage' => $namepage,
            'user' => $user,
            'events' => $events,
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

    /**
     * @Route("/user/my-rating", name="UserRating")
     */
    public function EventWithRating()
    {
        $namepage = 'My Rated Event';
        $user = $this->getUser();
        $events = $this->commentsRepository->findBy(['user'=>$user]);
        $nbEvents = (count($events)/6);
        $page = 0;

        return $this->render('user/event/my-rating.html.twig', [
            'events' => $events,
            'namepage' => $namepage,
            'nbEvents' => $nbEvents,
            'page' => $page
        ]);
    }

    /**
     * @Route("/user/event_rating/ajax", name="event_ajax_user_rating")
     */
    public function EventAjax(Request $request)
    {
        $now = new \DateTime();
        $results = [];
        $title = $request->get('title');
        $page = $request->get('page');
        $user = $this->getUser();
        $ids = $this->commentsRepository->getUserEventRating($user);

        $events = $this->eventRepository->getEventUserByTitle($title, $now, $page, $ids);

        $user = $this->getUser();
        if ($user) {
            foreach ($events as $event) {
                $comments = $this->commentsRepository->findOneBy(['user' => $user, 'event' => $event]);
                if ($comments) {
                    $results[] = $this->prepareResult($event);
                }
            }
        }

        return $this->json([
            'results' => $results,
            'title'=>$title,
            'page'=>$page
        ]);
    }
}
