<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\RegisterUserType;
use App\Manager\CommentsManager;
use App\Manager\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $eventManager;
    private $commentsManager;

    public function __construct(EventManager $eventManager, CommentsManager $commentsManager)
    {
        $this->eventManager = $eventManager;
        $this->commentsManager = $commentsManager;
    }

    protected function prepareResult(Event $event)
    {
        $vote = false;
        $user = $this->getUser();
        if ($user) {
            $comment = $this->commentsManager->findOneBy(['user' => $user, 'event' => $event]);
            if ($comment) {
                $vote = true;
            }
        }
        return [
            'html' => $this->renderView('home/event.html.twig', [
                'event' => $event,
                'vote' => $vote
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
        $events = $this->eventManager->findAll();
        $comments = $this->commentsManager->userLastRateEvent();

        return $this->render('user/dashboard.html.twig', [
            'namepage' => $namepage,
            'user' => $user,
            'events' => $events,
            'comments' => $comments,
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
        $events = $this->commentsManager->findBy(['user'=>$user]);
        $nbEvents = (count($events)/6);

        //Give number page for paging
        $nb = $this->eventManager->getNumberPage($nbEvents);

        $page = 0;

        return $this->render('user/event/my-rating.html.twig', [
            'events' => $events,
            'namepage' => $namepage,
            'nb' => $nb,
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
        $events = $this->eventManager->getEventByTitle($title, $now, $page);

        $user = $this->getUser();
        if ($user) {
            foreach ($events as $event) {
                $comments = $this->commentsManager->findOneBy(['user' => $user, 'event' => $event]);
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
