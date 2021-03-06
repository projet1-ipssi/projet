<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Event;
use App\Manager\CommentsManager;
use App\Manager\EventsManager;
use App\Repository\CommentsRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class EventController extends AbstractController
{
    private $eventsManager;
    private $commentsManager;
    private $em;

    public function __construct(EventsManager $eventsManager, CommentsManager $commentsManager, EntityManagerInterface $em)
    {
        $this->eventsManager = $eventsManager;
        $this->commentsManager = $commentsManager;
        $this->em = $em;
    }

    protected function prepareResult(Event $event)
    {
        $vote = false;
        $user = $this->getUser();
        $avg = $this->commentsManager->getAverage($event);

        if ($user) {
            $comment = $this->commentsManager->findOneBy(['user' => $user, 'event' => $event]);
            if ($comment) {
                $vote = true;
            }
        }

        return [
            'html' => $this->renderView('home/event.html.twig', [
                'event' => $event,
                'vote' => $vote,
                'moyenne' => $avg
            ])
        ];
    }

    /**
     * @Route("/event/profile-{id}", name="event_profile")
     */
    public function index($id)
    {
        $user = $this->getUser();
        $event = $this->eventsManager->find($id);
        $comments = null;
        $avg = $this->commentsManager->getAverage($event);

        if ($user) {
            $comments = $this->commentsManager->findOneBy(['user' => $user, 'event' => $event]);
            if ($comments) {
                $rating = $comments->getRating();
            } else {
                $rating = 0;
            }
        } else {
            $rating = 0;
        }


        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
            'event' => $event,
            'comment' => $comments,
            'rating' => $rating,
            'moyenne' => $avg
        ]);
    }

    /**
     * @Route("/event/rating/{id}", name="event_rating")
     */
    public function EventRating($id, Request $request)
    {

        $score = $request->get('environment_rating');
        $user = $this->getUser();
        $event = $this->eventsManager->find($id);

        $comment = $this->commentsManager->findOneBy(['user' => $user, 'event' => $event]);

        if ($comment) {
            $comment->setRating($score);
            $this->em->persist($comment);
            $this->em->flush();
        } else {
            $rating = new Comments();
            $rating->setUser($user);
            $rating->setEvent($event);
            $rating->setRating($score);
            $this->em->persist($rating);
            $this->em->flush();
        }

        $this->addFlash('success', 'Your are successfully Voted !');
        return $this->redirectToRoute('event_profile', ['id' => $id]);
    }

    /**
     * @Route("/event-rating", name="eventRating")
     */
    public function EventWithRating()
    {
        $events = $this->commentsManager->getEventRating();
        $nbEvents = (count($events) / 6);
        $page = 0;

        return $this->render('event/event_rating.html.twig', [
            'controller_name' => 'HomeController',
            'nbEvents' => $nbEvents,
            'events' => $events,
            'page' => $page
        ]);
    }

    /**
     * @Route("/event-without-rating", name="eventWithoutRating")
     */
    public function EventWithoutRating()
    {
        $now = new \DateTime();
        $ids = $this->commentsManager->getEventRating();
        $events = $this->eventsManager->getEventNoRating($ids, $now);
        $nbEvents = (count($events) / 6);
        $page = 0;

        return $this->render('event/event_without_rating.html.twig', [
            'controller_name' => 'HomeController',
            'nbEvents' => $nbEvents,
            'events' => $events,
            'page' => $page
        ]);
    }

    /**
     * @Route("/event_rating/ajax", name="event_ajax_rating")
     */
    public function EventAjax(Request $request)
    {

        $now = new \DateTime();
        $title = $request->get('title');
        $page = $request->get('page');
        $results = [];
        $ids = $this->commentsManager->getEventRating();
        $events = $this->eventsManager->getEventRatingByTitle($title, $now, $page, $ids);

        foreach ($events as $event) {
            $results[] = $this->prepareResult($event);
        }

        return $this->json([
            'results' => $results,
            'title' => $title
        ]);
    }

    /**
     * @Route("/event-without-rating/ajax", name="event_ajax_without_rating")
     */
    public function EventAjaxWithoutRating(Request $request)
    {

        $now = new \DateTime();
        $title = $request->get('title');
        $page = $request->get('page');
        $results = [];

        $ids = $this->commentsManager->getEventRating();

        $events = $this->eventsManager->getEventWithoutRatingByTitle($title, $now, $page, $ids);
        foreach ($events as $event) {
            $results[] = $this->prepareResult($event);
        }

        return $this->json([
            'results' => $results,
            'title' => $title
        ]);
    }
}
