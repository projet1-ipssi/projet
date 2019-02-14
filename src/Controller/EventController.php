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
        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
            'event'=>$event,
            'comment'=>$comments,
            'rating'=>$rating
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

        return $this->redirectToRoute('event_profile', ['id'=>$id]);
    }

    /**
     * @Route("/event-rating", name="eventRating")
     */
    public function EventWithRating(CommentsRepository $commentsRepository)
    {
        return $this->render('event/event_rating.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/event-without-rating", name="eventWithoutRating")
     */
    public function EventWithoutRating(CommentsRepository $commentsRepository)
    {
        return $this->render('event/event_without_rating.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/event_rating/ajax", name="event_ajax_rating")
     */
    public function EventAjax(Request $request, CommentsRepository $commentsRepository){

        $now = new \DateTime();
        $title = $request->get('title');
        $results=[];

        $events = $this->getDoctrine()->getRepository(Event::class)->getEventByTitle($title, $now);
        foreach ($events as $event){
                $comments = $commentsRepository->findOneBy(['event'=>$event]);
                if ($comments){
                    $results[] = $this->prepareResult($event);
                }
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

        $events = $this->getDoctrine()->getRepository(Event::class)->getEventByTitle($title, $now, $page);
        foreach ($events as $event){
            $comments = $commentsRepository->findOneBy(['event'=>$event]);
            if (!$comments){
                $results[] = $this->prepareResult($event);
            }
        }

        return $this->json([
            'results' => $results,
            'title'=>$title
        ]);
    }
}
