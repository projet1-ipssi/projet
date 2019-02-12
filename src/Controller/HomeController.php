<?php

namespace App\Controller;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    protected function prepareResult(Event $event )
    {
            return [
                'html' => $this->renderView('home/event.html.twig', [
                    'event' => $event,
                ])
            ];
    }

    /**
     * @Route("/event/ajax", name="event_ajax")
     */
    public function EventAjax(Request $request){

        $now = new \DateTime();
        $title = $request->get('title');
        $results=[];

        $events = $this->getDoctrine()->getRepository(Event::class)->getEventByTitle($title, $now);

        foreach ($events as $event){
            $results[] = $this->prepareResult($event);
        }

        return $this->json([
            'results' => $results,
            'title'=>$title
        ]);
    }
}
