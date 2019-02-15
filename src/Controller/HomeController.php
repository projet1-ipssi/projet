<?php

namespace App\Controller;

use App\Entity\Event;
use App\Manager\CommentsManager;
use App\Manager\EventsManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    private $eventsManager;
    private $commentsManager;

    public function __construct(EventsManager $eventsManager, CommentsManager $commentsManager)
    {
        $this->eventsManager = $eventsManager;
        $this->commentsManager = $commentsManager;
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

    //Display Home Events

    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $events = $this->eventsManager->findAll();
        $nbEvents = (count($events) / 6);
        $page = 0;


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'nbEvents' => $nbEvents,
            'events' => $events,
            'page' => $page
        ]);
    }

    //Ajax request Home Events

    /**
     * @Route("/event/ajax", name="event_ajax")
     */
    public function EventAjax(Request $request)
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
}
