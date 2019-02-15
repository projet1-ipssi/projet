<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\AddEventType;
use App\Form\RegisterUserType;
use App\Manager\CommentsManager;
use App\Manager\EventsManager;
use App\Manager\UsersManager;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    private $usersManager;
    private $eventsManager;
    private $commentsManager;
    private $em;

    public function __construct(UsersManager $usersManager, EventsManager $eventsManager, CommentsManager $commentsManager, EntityManagerInterface $em)
    {
        $this->usersManager = $usersManager;
        $this->eventsManager = $eventsManager;
        $this->commentsManager = $commentsManager;
        $this->em = $em;
    }

    //Return Event template for Admin Noted Page
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
        $avg = $this->commentsManager->getAverage($event);

        return [
            'html' => $this->renderView('home/event.html.twig', [
                'event' => $event,
                'vote' => $vote,
                'moyenne' => $avg
            ])
        ];
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        //Name Page
        $namepage = 'Dashboard';

        $lastUsers = $this->usersManager->findLastUsers();
        $users = $this->usersManager->findAll();
        $events = $this->eventsManager->findAll();
        $comments = $this->commentsManager->getTopTen();

        return $this->render('admin/dashboard.html.twig', [
            'namepage' => $namepage,
            'users' => $users,
            'lastUsers' => $lastUsers,
            'events' => $events,
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/admin/profile", name="admin-profile")
     */
    public function profile(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $namepage = 'My account';

        $user = $this->getUser();

        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Your profile update successfully !');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/profile/profile.html.twig', [
            'form' => $form->createView(),
            'namepage' => $namepage,
        ]);
    }

    /**
     * @Route("/admin/users", name="all-users")
     */
    public function users()
    {
        $namepage = 'All Users';

        $users = $this->usersManager->findAll();

        return $this->render('admin/user/all.html.twig', [
            'users' => $users,
            'namepage' => $namepage,
        ]);
    }

    /**
     * @Route("/admin/users/update/{id}", name="user-update")
     */
    public function updateUser($id, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $namepage = 'Update User';

        $user = $this->usersManager->find($id);

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
        $namepage = 'Add User';

        $user = new User();

        $form = $this->createForm(RegisterUserType::class, $user);
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
            'namepage' => $namepage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/users/remove/{id}", name="user-remove")
     */
    public function removeUser($id)
    {
        $user = $this->usersManager->find($id);
        $comments = $this->commentsManager->findBy(['user' => $user]);

        if ($user == $this->getUser()) {
            $this->addFlash('danger', 'You can not delete your account !');
            return $this->redirectToRoute('all-users');
        } else {
            foreach ($comments as $comment) {
                $this->em->remove($comment);
            }

            $this->em->remove($user);
            $this->em->flush();
        }

        $this->addFlash('success', 'You are successfully remove user!');
        return $this->redirectToRoute('all-users');
    }

    //Display All Events

    /**
     * @Route("/admin/events", name="all-event")
     */
    public function AllEvent()
    {
        $namepage = 'All Event';

        $events = $this->eventsManager->findAll();
        $nbEvents = (count($events) / 6);

        //Give number page for paging
        $nb = $this->eventsManager->getNumberPage($nbEvents);

        $page = 0;


        return $this->render('admin/event/all.html.twig', [
            'namepage' => $namepage,
            'events' => $events,
            'nb' => $nb,
            'nbEvents' => $nbEvents,
            'page' => $page
        ]);
    }


    //Ajax request All event

    /**
     * @Route("admin/event/ajax", name="all_event_ajax")
     */
    public function AllEventAjax(Request $request)
    {
        $now = new \DateTime();
        $title = $request->get('title');
        $page = $request->get('page');
        $results = [];

        $events = $this->eventsManager->getEventByTitle($title, $now, $page);

        foreach ($events as $event) {
            $results[] = $this->prepareResult($event);
        }

        return $this->json([
            'results' => $results,
            'title' => $title,
            'page' => $page
        ]);
    }


    /**
     * @Route("admin/event/add", name="add-event")
     */
    public function addEvent(Request $request, MailerService $mailerService)
    {
        $namepage = 'Add Event';

        $now = new \DateTime();
        $event = new Event();

        $form = $this->createForm(AddEventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($event->getStartDate() == $event->getEndDate()) {
                $this->addFlash('danger', 'The end date of the event must be different from the start date !');
            } elseif ($event->getStartDate() > $event->getEndDate()) {
                $this->addFlash('danger', 'The event end date must be upper than the start date.!');
            } elseif ($event->getStartDate() <= $now) {
                $this->addFlash('danger', 'The event start date must be upper than now.!');
            } else {
                $this->em->persist($event);
                $this->em->flush();
                $users = $this->usersManager->findAll();
                foreach ($users as $user) {
                    $mailerService->sendMail($user, $event);
                }

                $this->addFlash('success', 'Event added successfully !');
                return $this->redirectToRoute('admin');
            }

        }

        return $this->render('admin/event/add.html.twig', [
            'namepage' => $namepage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/update/event/{id}", name="update-event")
     */
    public function adminUpdateVideo(Request $request, $id)
    {
        $namepage = 'Update Event';
        $event = $this->eventsManager->find($id);

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
    public function removeEvent($id)
    {
        $event = $this->eventsManager->find($id);
        $comments = $this->commentsManager->findBy(['event' => $event]);

        foreach ($comments as $comment) {
            $this->em->remove($comment);
        }

        $this->em->remove($event);
        $this->em->flush();

        $this->addFlash('success', 'You are successfully remove Event!');
        return $this->redirectToRoute('admin');
    }

    //Display Only Admin Rating Events

    /**
     * @Route("/admin/my-rating", name="AdminRating")
     */
    public function AdminRatingPage()
    {
        $namepage = 'My Rated Event';

        $user = $this->getUser();
        $events = $this->commentsManager->findBy(['user' => $user]);
        $nbEvents = (count($events) / 6);


        //Give number page for paging
        $nb = $this->eventsManager->getNumberPage($nbEvents);

        $page = 0;

        return $this->render('admin/event/my-rating.html.twig', [
            'namepage' => $namepage,
            'events' => $events,
            'nb' => $nb,
            'nbEvents' => $nbEvents,
            'page' => $page
        ]);
    }

    //Ajax request Admin Rating Events

    /**
     * @Route("/admin/all_event_rating/ajax", name="event_ajax_admin_rating")
     */
    public function EventAjax(Request $request)
    {
        $now = new \DateTime();
        $results = [];
        $title = $request->get('title');
        $page = $request->get('page');
        $user = $this->getUser();
        $ids = $this->commentsManager->getUserEventRating($user);
        $events = $this->eventsManager->getEventUserByTitle($title, $now, $page, $ids);

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
            'title' => $title,
            'page' => $page
        ]);
    }
}
