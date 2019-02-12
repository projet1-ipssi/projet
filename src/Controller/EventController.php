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
}
