<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Event;
use App\Repository\CommentsRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class EventController extends AbstractController
{

    protected function prepareResult(Event $event )
    {
        $vote = false;
        $user = $this->getUser();
        if ($user){
            $comment = $this->getDoctrine()->getRepository(Comments::class)->findOneBy(['user'=>$user, 'event'=>$event]);
            if ($comment){
                $vote = true;
            }
        }
        $avg = $this->getDoctrine()->getRepository(Comments::class)->getMoyenne($event);

        return [
            'html' => $this->renderView('home/event.html.twig', [
                'event' => $event,
                'vote'=>$vote,
                'moyenne'=>$avg
            ])
        ];
    }

    /**
     * @Route("/event/profile-{id}", name="event_profile")
     */
    public function index($id, EventRepository $eventRepository, CommentsRepository $commentsRepository)
    {
        $user = $this->getUser();
        $event = $eventRepository->find($id);
        $comments = null;
        if ($user){
            $comments = $commentsRepository->findOneBy(['user'=>$user, 'event'=>$event]);
            if ($comments){
            $rating = $comments->getRating();
            }
            else{
                $rating = 0;
            }
        }
        else{
            $rating = 0;
        }
        $avg = $this->getDoctrine()->getRepository(Comments::class)->getMoyenne($event);

        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
            'event'=>$event,
            'comment'=>$comments,
            'rating'=>$rating,
            'moyenne'=>$avg
        ]);
    }

    /**
     * @Route("/event/rating/{id}", name="event_rating")
     */
    public function EventRating($id,Request $request, EventRepository $eventRepository, EntityManagerInterface $entityManager, CommentsRepository $commentsRepository){

        $score = $request->get('environment_rating');
        $user = $this->getUser();
        $event = $eventRepository->find($id);

        $comment = $commentsRepository->findOneBy(['user'=>$user, 'event'=>$event]);

        if ($comment){
            $comment->setRating($score);
            $entityManager->persist($comment);
            $entityManager->flush();
        }
        else{
            $rating = new Comments();
            $rating->setUser($user);
            $rating->setEvent($event);
            $rating->setRating($score);
            $entityManager->persist($rating);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Your are successfully Voted !');
        return $this->redirectToRoute('event_profile', ['id'=>$id]);
    }

    /**
     * @Route("/event-rating", name="eventRating")
     */
    public function EventWithRating(CommentsRepository $commentsRepository, EventRepository $eventRepository)
    {
        $now = new \DateTime();
        $events = $commentsRepository->getEventRating();
        $nbEvents = (count($events)/6);
        $page = 0;

        return $this->render('event/event_rating.html.twig', [
            'controller_name' => 'HomeController',
            'nbEvents'=>$nbEvents,
            'events'=>$events,
            'page'=>$page
        ]);
    }

    /**
     * @Route("/event-without-rating", name="eventWithoutRating")
     */
    public function EventWithoutRating(CommentsRepository $commentsRepository, EventRepository $eventRepository)
    {
        $now = new \DateTime();
        $ids = $commentsRepository->getEventRating();
        $events = $eventRepository->getEventNoRating($ids,$now);
        $nbEvents = (count($events)/6);
        $page = 0;

        return $this->render('event/event_without_rating.html.twig', [
            'controller_name' => 'HomeController',
            'nbEvents'=>$nbEvents,
            'events'=>$events,
            'page'=>$page
        ]);
    }

    /**
     * @Route("/event_rating/ajax", name="event_ajax_rating")
     */
    public function EventAjax(Request $request, CommentsRepository $commentsRepository){

        $now = new \DateTime();
        $title = $request->get('title');
        $page = $request->get('page');
        $results=[];

        $ids = $commentsRepository->getEventRating();

        $events = $this->getDoctrine()->getRepository(Event::class)->getEventRatingByTitle($title, $now, $page, $ids);
        foreach ($events as $event){
                    $results[] = $this->prepareResult($event);
        }

        return $this->json([
            'results' => $results,
            'title'=>$title
        ]);
    }

    /**
     * @Route("/event-without-rating/ajax", name="event_ajax_without_rating")
     */
    public function EventAjaxWithoutRating(Request $request, CommentsRepository $commentsRepository){

        $now = new \DateTime();
        $title = $request->get('title');
        $page = $request->get('page');
        $results=[];

        $ids = $commentsRepository->getEventRating();

        $events = $this->getDoctrine()->getRepository(Event::class)->getEventWithoutRatingByTitle($title, $now, $page, $ids);
        foreach ($events as $event){
            $results[] = $this->prepareResult($event);
        }

        return $this->json([
            'results' => $results,
            'title'=>$title
        ]);
    }
}
